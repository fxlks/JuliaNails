<template>
  <AmSlidePopup
    v-if="props.visibility"
    :visibility="props.visibility"
    class="am-cqcs__dialog"
    position="top"
    :footer-visibility="false"
    @update:visibility="(value) => emits('update:visibility', value)"
  >
    <template #header>
      <div class="am-cqcs__header">
        {{ amLabels.e_ticket_scanner }}
      </div>
    </template>

    <div v-if="props.visibility" class="am-cqcs__container">
      <div v-show="!dataLoading" class="am-cqcs__container-inner">
        <div class="am-cqcs__camera-container">
          <video
            ref="videoElement"
            class="am-cqcs__video"
            :style="{ display: isScanning ? 'block' : 'none' }"
            autoplay
            playsinline
          />
          <canvas
            ref="canvasElement"
            class="am-cqcs__canvas"
            style="display: none"
          />

          <!-- Scanner overlay -->
          <div class="am-cqcs__video-overlay">
            <div class="am-cqcs__frame" />
          </div>
        </div>

        <div v-if="responseData" class="am-cqcs__response-info">
          <span
            :class="
              responseData.messageType === 'error'
                ? 'am-icon-close-filled'
                : 'am-icon-checkmark-circle-full'
            "
          />
          <div v-if="responseData.message" class="am-cqcs__response-message">
            {{ amLabels[responseData.message] || responseData.message }}
          </div>
          <div v-if="responseData.eventName" class="am-cqcs__response-name">
            {{ responseData.eventName }}
          </div>
          <div
            v-if="responseData.ticketManualCode"
            class="am-cqcs__response-item"
          >
            <span>{{amLabels.ticket_id}}:</span> #{{ responseData.ticketManualCode }}
          </div>
          <div
            v-if="responseData.eventTicketName"
            class="am-cqcs__response-item"
          >
            <span>{{amLabels.ticket_name}}:</span> {{ responseData.eventTicketName }}
          </div>
          <div v-if="responseData.bookingId" class="am-cqcs__response-item">
            <span>{{amLabels.booking_id}}:</span> #{{ responseData.bookingId }}
          </div>
          <div
            v-if="responseData.ticketControlNumber"
            class="am-cqcs__response-item"
          >
            <span>{{amLabels.attendees_allowed}}:</span> {{ responseData.ticketControlNumber }}
          </div>
        </div>

        <div v-if="scanError" class="am-cqcs__error">
          <span class="am-icon-close-filled" />
          {{ scanError }}
        </div>

        <div class="am-cqcs__controls">
          <AmButton
            v-if="torchSupported"
            category="primary"
            @click="toggleTorch"
          >
            <span :class="torchActive ? 'am-icon-bolt-lightning' : 'am-icon-bolt-lightning-fill'" />
            {{ torchActive ? amLabels.torch_off : amLabels.torch_on }}
          </AmButton>

          <AmButton v-if="cameras.length > 1" @click="switchCamera">
            <span class="am-icon-refresh" />
            {{amLabels.switch_camera}}
          </AmButton>

          <AmButton
            v-if="!isScanning"
            category="primary"
            @click="startScanning"
          >
            <span class="am-icon-mobile" />
            {{amLabels.start_scanner}}
          </AmButton>

          <AmButton v-if="isScanning" category="danger" @click="stopScanning">
            <span class="am-icon-mobile" />
            {{amLabels.stop_scanner}}
          </AmButton>
        </div>

        <!-- Manual form -->
        <AmCollapse class="am-cqcs__collapse">
          <AmCollapseItem
            ref="manualCodeCollapse"
            class="am-cqcs__collapse-item"
          >
            <template #heading>
              {{amLabels.enter_ticket_manually}}
            </template>
            <el-form
              ref="manualCodeForm"
              :model="manualCode"
              :rules="rules"
              class="am-cqcs__form"
              @submit.prevent="processManualInput"
            >
              <template
                v-for="(item, name) in manualCodeConstruction"
                :key="name"
              >
                <component
                  :is="item.template"
                  v-model="manualCode[name]"
                  v-bind="item.props"
                />
              </template>
              <el-form-item>
                <AmButton category="primary" @click="processManualInput">
                  {{amLabels.validate_ticket}}
                </AmButton>
              </el-form-item>
            </el-form>
          </AmCollapseItem>
        </AmCollapse>
        <!-- /Manual form -->
      </div>
      <Skeleton v-if="dataLoading"></Skeleton>
    </div>
  </AmSlidePopup>
</template>

<script setup>
// * Import from Vue
import { ref, watch, onMounted, inject, onUnmounted } from 'vue'

// * Import from Vuex
import { useStore } from 'vuex'

// * Components
import AmSlidePopup from '../../../../_components/slide-popup/AmSlidePopup.vue'
import AmButton from '../../../../_components/button/AmButton.vue'
import AmCollapse from '../../../../_components/collapse/AmCollapse.vue'
import AmCollapseItem from '../../../../_components/collapse/AmCollapseItem.vue'
import Skeleton from './Skeleton.vue'

// * Import Form Templates
import { formFieldsTemplates } from '../../../../../assets/js/common/formFieldsTemplates'

// * Libraries
import jsQR from 'jsqr'
import moment from 'moment/moment'

// * Composables
import httpClient from '../../../../../plugins/axios'
import { useAuthorizationHeaderObject } from '../../../../../assets/js/public/panel'

// * Props
const props = defineProps({
  visibility: {
    type: Boolean,
    default: false,
  },
})

// * Store
let store = useStore()

// * Emits
const emits = defineEmits(['update:visibility'])

// * Labels
let amLabels = inject('labels')

let dataLoading = ref(false)
let isScanning = ref(false)
let torchActive = ref(false)
let torchSupported = ref(false)
let cameras = ref([])
let currentCameraIndex = ref(0)
let scanError = ref('')
let stream = ref(null)

// Added for jsQR loop
let frameRequestId = ref(null)
let lastDecodedAt = ref(0)
let decodeCooldownMs = ref(1500)

let responseData = ref(null)

// * Refs
let videoElement = ref(null)
let canvasElement = ref(null)

// * Manual Code Form
// Reference to the form
let manualCodeForm = ref(null)
// Form data
let manualCode = ref({
  ticketCode: '',
})

// Form validation rules
let rules = ref({
  ticketCode: [
    {
      required: true,
      message: 'Ticket code is required',
      trigger: 'click',
    },
  ],
})

let manualCodeConstruction = ref({
  ticketCode: {
    template: formFieldsTemplates.text,
    props: {
      itemName: 'ticketCode',
      placeholder: amLabels.enter_ticket_code,
      class: 'am-cqcs__form-item',
      prepend: '#',
    },
  },
})

// Reference to the collapse component
let manualCodeCollapse = ref(null)

async function initializeCamera() {
  try {
    // Check if we're on HTTPS, localhost, or 127.0.0.1
    const isSecureContext =
      window.isSecureContext ||
      location.protocol === 'https:' ||
      location.hostname === 'localhost' ||
      location.hostname === '127.0.0.1' ||
      location.hostname.includes('ngrok.io') ||
      location.hostname.startsWith('192.168.') ||
      location.hostname.startsWith('10.') ||
      location.hostname.startsWith('172.')

    if (!isSecureContext) {
      throw new Error(
        'Camera access requires HTTPS or localhost. Please access this page over a secure connection or use localhost.'
      )
    }

    // Check if MediaDevices API is supported
    if (!navigator.mediaDevices) {
      throw new Error(
        'MediaDevices API not supported in this browser. Please update your browser or try Chrome/Firefox.'
      )
    }

    if (!navigator.mediaDevices.getUserMedia) {
      throw new Error(
        'getUserMedia not supported in this browser. Please update your browser or try Chrome/Firefox.'
      )
    }

    // Try to get available cameras with permission check
    let devices = []
    try {
      devices = await navigator.mediaDevices.enumerateDevices()
    } catch (enumError) {
      console.warn('Could not enumerate devices:', enumError)
      // Try to proceed without device enumeration
    }

    cameras.value = devices.filter((device) => device.kind === 'videoinput')

    // Even if we can't enumerate devices, try to start scanning
    // The browser will handle camera selection
    await startScanning()
  } catch (error) {
    handleScannerError(error)
  }
}

async function startScanning() {
  if (manualCodeCollapse.value) {
    manualCodeCollapse.value.closingFromParent()
  }
  responseData.value = null
  try {
    scanError.value = ''

    // Check MediaDevices support again
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      throw new Error('Camera access not supported in this browser')
    }

    // Stop any existing stream
    if (stream.value) {
      stream.value.getTracks().forEach((track) => track.stop())
    }

    // Start with basic constraints
    let constraints = {
      video: {
        facingMode: 'environment', // Prefer back camera
      },
    }

    // If we have specific camera selected, use it
    if (
      cameras.value.length > 0 &&
      cameras.value[currentCameraIndex.value] &&
      cameras.value[currentCameraIndex.value].deviceId
    ) {
      constraints.video.deviceId = {
        exact: cameras.value[currentCameraIndex.value].deviceId,
      }
    } else {
      // Try with additional constraints for better quality
      constraints.video = {
        facingMode: 'environment',
        width: { ideal: 1280, min: 640 },
        height: { ideal: 720, min: 480 },
      }
    }

    // Get media stream
    stream.value = await navigator.mediaDevices.getUserMedia(constraints)

    // Check torch support
    const track = stream.value.getVideoTracks()[0]
    if (track && track.getCapabilities) {
      const capabilities = track.getCapabilities()
      torchSupported.value = !!capabilities.torch
    }

    // Attach stream to video element
    if (videoElement.value) {
      videoElement.value.srcObject = stream.value

      // Wait for video to load
      videoElement.value.onloadedmetadata = () => {
        videoElement.value.play().catch((e) => {
          console.warn('Could not auto-play video:', e)
        })
      }
    }

    isScanning.value = true

    // Start QR code detection (simplified version)
    startQRDetection()
  } catch (error) {
    handleScannerError(error)
  }
}

function stopScanning() {
  responseData.value = null
  isScanning.value = false
  if (frameRequestId.value) {
    cancelAnimationFrame(frameRequestId.value)
    frameRequestId.value = null
  }
  if (stream.value) {
    stream.value.getTracks().forEach((t) => t.stop())
    stream.value = null
  }
  if (videoElement.value) videoElement.value.srcObject = null
}

function startQRDetection() {
  const video = videoElement.value
  const canvas = canvasElement.value
  if (!video || !canvas) return
  const ctx = canvas.getContext('2d', { willReadFrequently: true })

  const scan = () => {
    if (!isScanning.value) return
    if (video.readyState === video.HAVE_ENOUGH_DATA) {
      if (
        canvas.width !== video.videoWidth ||
        canvas.height !== video.videoHeight
      ) {
        canvas.width = video.videoWidth
        canvas.height = video.videoHeight
      }
      ctx.drawImage(video, 0, 0, canvas.width, canvas.height)
      const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height)

      const now = performance.now()
      if (now - lastDecodedAt.value >= decodeCooldownMs.value) {
        const qr = jsQR(imageData.data, imageData.width, imageData.height, {
          inversionAttempts: 'dontInvert',
        })
        if (qr && qr.data) {
          if (qr.data) {
            onTicketScan(qr.data)
            stopScanning()
          }
          lastDecodedAt.value = now
        }
      }
    }
    frameRequestId.value = requestAnimationFrame(scan)
  }

  frameRequestId.value = requestAnimationFrame(scan)
}

async function toggleTorch() {
  if (!torchSupported.value || !stream.value) return

  try {
    const track = stream.value.getVideoTracks()[0]
    await track.applyConstraints({
      advanced: [{ torch: !torchActive.value }],
    })
    torchActive.value = !torchActive.value
  } catch (error) {
    console.error('Error toggling torch:', error)
  }
}

async function switchCamera() {
  if (cameras.value.length <= 1) return

  currentCameraIndex.value =
    (currentCameraIndex.value + 1) % cameras.value.length
  await startScanning()
}

function processManualInput() {
  manualCodeForm.value.validate((valid) => {
    if (valid) {
      const ticketData = {
        ticketManualCode: manualCode.value.ticketCode.trim(),
        scannedAt: moment().format('YYYY-MM-DD'),
      }
      sendTicketData(ticketData)
      // Clear form
      manualCode.value.ticketCode = ''
    } else {
      return false
    }
  })
}

function parseQrCodeTicketString(string) {
  const obj = {}
  const regex = /(\w+)\s*:\s*([^\s|]+)/g
  let m
  while ((m = regex.exec(string)) !== null) {
    let key = m[1]
    let value = m[2]
    if (/^-?\d+$/.test(value)) {
      value = Number(value)
    }
    obj[key] = value
  }
  return obj
}

function onTicketScan(ticketCode) {
  scanError.value = ''
  let ticketDataResult = parseQrCodeTicketString(ticketCode)
  const scannedAt = moment().format('YYYY-MM-DD')

  let ticketData = {
    ...ticketDataResult,
    scannedAt,
  }

  sendTicketData(ticketData)
}

function sendTicketData(ticketData) {
  dataLoading.value = true
  scanError.value = ''
  // Call the backend API to validate and check in the ticket
  httpClient
    .post(
      '/scan-eticket',
      {
        ticketManualCode: ticketData.ticketManualCode,
        scannedAt: ticketData.scannedAt,
      },
      Object.assign(useAuthorizationHeaderObject(store), {
        params: {
          source: 'cabinet-provider',
        },
      })
    )
    .then((response) => {
      if (response.data && response.data.data) {
        responseData.value = {
          messageType: response.data.data.messageType,
          message: response.data.data.message,
          bookingId: response.data.data.bookingId,
          ticketManualCode: response.data.data.ticketManualCode,
          ticketControlNumber: response.data.data.ticketControl,
          eventName: response.data.data.eventName ? response.data.data.eventName : getResponseEventName(response.data.data),
        }

        if (getResponseEventTicketName(response.data.data)) {
          responseData.value.eventTicketName = getResponseEventTicketName(
            response.data.data
          )
        }
      }
    })
    .catch((error) => {
      const resp = error?.response?.data

      if (!resp) return

      if (resp.data) {
        responseData.value = {
          messageType: resp.data.messageType,
          message: resp.data.message,
        }
      } else {
        responseData.value = {
          messageType: 'error',
          message: resp.message,
        }

        if (resp.message.includes('Mandatory fields not passed!')) {
          responseData.value.message = amLabels.ticket_not_valid
        }
      }
    })
    .finally(() => {
      dataLoading.value = false
    })
}

function getResponseEventName(data) {
  let qrItem = data.qrCodes.find(
    (qr) => qr.ticketManualCode === data.ticketManualCode
  )

  return qrItem.eventName || ''
}

function getResponseEventTicketName(data) {
  let qrItem = data.qrCodes.find(
    (qr) => qr.ticketManualCode === data.ticketManualCode
  )

  return qrItem.eventTicketName || ''
}

function handleScannerError(error) {
  console.error('Scanner error:', error)

  if (error.name === 'NotAllowedError') {
    scanError.value = amLabels.camera_error_1
  } else if (error.name === 'NotFoundError') {
    scanError.value = amLabels.camera_error_2
  } else if (error.name === 'NotSupportedError') {
    scanError.value = amLabels.camera_error_3
  } else {
    scanError.value = `Camera error: ${error.message}`
  }
}

onMounted(() => {
  watch(
    () => props.visibility,
    (newVal) => {
      if (newVal) {
        initializeCamera()
      } else {
        stopScanning()
      }
    },
    { immediate: true }
  )
})

onUnmounted(() => {
  stopScanning()
})
</script>

<style lang="scss">
// * QR Scanner Styles
@mixin qr-code-scanner {
  // - am   - amelia
  // - cqcs - cabinet-qr-code-scanner
  .am-cqcs__dialog {
    .am-cqcs__header {
      font-size: 18px;
      font-weight: 500;
      font-style: normal;
      line-height: 1.55;
      text-transform: initial;
      letter-spacing: initial;
      text-align: center;
      white-space: nowrap;
      color: var(--am-c-main-heading-text);
      padding: 0 0 16px;
      margin: 0;
    }
  }

  // Main scanner container
  .am-cqcs {
    // * Container for the entire scanner component
    &__container {
      position: relative;
      display: block;
      height: 626px;
      overflow-x: hidden;

      // Main Scroll styles
      &::-webkit-scrollbar {
        width: 6px;
      }

      &::-webkit-scrollbar-thumb {
        border-radius: 6px;
        background: var(--am-c-scroll-op30);
      }

      &::-webkit-scrollbar-track {
        border-radius: 6px;
        background: var(--am-c-scroll-op10);
      }

      // Inner wrapper to center content
      &-inner {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
        padding: 0;
        margin: 0;
      }
    }

    &__camera-container {
      position: relative;
      width: 100%;
      max-width: 500px;
      background: var(--am-c-spb-text);
      border-radius: 8px;
      overflow: hidden;
    }

    &__video {
      width: 100%;
      height: auto;
      display: block;
    }

    &__video-overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      pointer-events: none;
    }

    &__frame {
      width: 200px;
      height: 200px;
      border: 3px solid var(--am-c-primary);
      border-radius: 8px;
      background: transparent;
      position: relative;

      &::before,
      &::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        border: 3px solid var(--am-c-primary);
      }

      &::before {
        top: -10px;
        left: -10px;
        border-right: none;
        border-bottom: none;
        border-radius: 10px 0 0 0;
      }

      &::after {
        bottom: -10px;
        right: -10px;
        border-left: none;
        border-top: none;
        border-radius: 0 0 10px 0;
      }
    }

    &__controls {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      justify-content: center;

      [class^="am-icon-"] {
        font-size: 26px;
      }
    }

    &__error {
      color: var(--am-c-error);
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 12px 16px;
      background-color: var(--am-c-main-bgr);
      border-radius: 4px;
      border: 1px solid var(--am-c-error);
      text-align: center;
      max-width: 500px;
      font-size: 14px;
      [class^="am-icon-"] {
        font-size: 22px;
      }
    }

    &__response {
      &-info {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;

        .am-icon {
          &-checkmark-circle-full {
            color: var(--am-c-success);
            font-size: 38px;
          }
          &-close-filled {
            color: var(--am-c-error);
            font-size: 38px;
          }
        }
      }

      &-message {
        font-weight: bold;
        font-size: 26px;
        line-height: 1.5;
        text-align: center;
        color: var(--am-c-main-text);
      }

      &-name {
        font-size: 18px;
        font-weight: bold;
        text-align: center;
        margin-top: 16px;
        margin-bottom: 8px;
        color: var(--am-c-main-text);
      }

      &-item {
        font-size: 14px;
        font-weight: bold;
        color: var(--am-c-main-text);

        span {
          font-weight: normal;
          color: var(--am-c-main-text);
        }
      }
    }


    // Collapse for manual input
    &__collapse {
      width: 100%;
      max-width: 500px;

      &-item {
        width: 100%;
      }
    }

    &__form {
      width: 100%;
      max-width: 500px;
      padding: 0 16px;

      .el-form-item {
        &.is-required {
          .el-form-item__label {
            &:before {
              top: -8px;
            }
          }
        }

        .am-button {
          width: 100%;
        }
      }
    }
  }

  @media (max-width: 768px) {
    .am-cqcs {
      &__camera-container {
        max-width: 100%;
      }

      &__controls {
        flex-direction: column;
        align-items: center;

        .el-button {
          width: 200px;
        }
      }
    }
  }
}

.amelia-v2-booking #amelia-container {
  @include qr-code-scanner;
}
</style>
