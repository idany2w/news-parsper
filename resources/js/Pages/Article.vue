<script >
import { Head, Link } from '@inertiajs/inertia-vue3';

export default {
  props: {
    id: Number,
  },
  data() {
    return {
      count: 0
    }
  },
  methods: {}
}
</script>

<template>
    <Head title="Article" />

    <!-- <Link :href="route('article')" >
        Log in
    </Link> -->

    Article {{ id }}
</template>
