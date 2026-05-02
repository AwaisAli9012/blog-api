<script setup>
defineProps({
  posts: Array
})
</script>

<template>
  <div>
    <h1>All Posts</h1>
    <ul>
      <li v-for="post in posts" :key="post.id">
        <strong>{{ post.title }}</strong> - {{ post.content }}
      </li>
    </ul>
  </div>
</template>
