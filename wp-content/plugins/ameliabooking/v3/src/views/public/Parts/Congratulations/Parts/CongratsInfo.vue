<template>
  <div class="am-congrats__info">
    <slot name="info"></slot>
    <span class="am-congrats__info-divider"></span>
    <slot name="customer"></slot>
  </div>
</template>

<script>
export default {
  name: "CongratsInfo"
}
</script>

<style lang="scss">
@mixin congrats-info-block {
  .am-congrats__info {
    display: flex;
    flex-direction: column;
    align-items: stretch;

    &-divider {
      border-top: 1px solid var(--am-c-congrats-text-op30);
      padding-top: 16px;
    }

    & > div {
      $count: 20;
      @for $i from 0 through $count {
        &:nth-child(#{$i + 1}) {
          animation: 600ms cubic-bezier(.45,1,.4,1.2) #{$i*100}ms am-animation-slide-up;
          animation-fill-mode: both;
        }
      }
    }
  }
}

// Public
.amelia-v2-booking #amelia-container {
  @include congrats-info-block;
}

// Admin
#amelia-app-backend-new #amelia-container {
  @include congrats-info-block;
}
</style>