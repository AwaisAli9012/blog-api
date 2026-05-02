<template>
  <div>
    <h1>All Posts</h1>
    <ul>
      <li v-for="post in posts" :key="post.id">
        {{ post.title }} - {{ post.content }}
      </li>
    </ul>
  </div>
</template>

<script setup>
defineProps({ posts: Array });
</script>
