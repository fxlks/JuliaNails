<template>
  <el-skeleton
    animated
    class="am-efi-skel"
  >
    <template #template>
      <el-skeleton-item class="am-efi-skel__heading" variant="text"></el-skeleton-item>
      <div>
        <el-skeleton-item class="am-efi-skel__tab" variant="text"></el-skeleton-item>
        <el-skeleton-item class="am-efi-skel__tab" variant="text"></el-skeleton-item>
      </div>
      <el-skeleton-item class="am-efi-skel__text" variant="text"></el-skeleton-item>
    </template>
  </el-skeleton>
</template>

<script>
export default {
  name: "EventInfoSkeleton"
}
</script>

<style lang="scss">
@mixin am-efi-skeleton {
  // am - amelia
  // efi -
  .am-efi-skel {
    height: 650px;
    padding: 16px 24px;

    &__heading {
      height: 80px;
      margin-bottom: 12px;
    }

    &__tab {
      width: 65px;
      height: 40px;
      margin: 0 8px 8px 0;
    }

    &__text {
      width: 80%;
      height: 400px;
    }
  }
}

// public
.amelia-v2-booking #amelia-container {
  @include am-efi-skeleton;
}

// admin
#amelia-app-backend-new {
  @include am-efi-skeleton;
}
</style>