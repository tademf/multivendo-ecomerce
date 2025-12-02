<template>
  <div>
    <Head>
      <title>GreenMarket - B2B E-Commerce Platform</title>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </Head>

    <slot />
  </div>
</template>

<script>
import { Head } from '@inertiajs/vue3'

export default {
  components: {
    Head
  }
}
</script>
