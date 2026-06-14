<template>
  <div class="container">
    <div class="flex justify-center">
      <div class="w-full">
        <nav class="navbar bg-gray-100 mb-4">
          <a href="/admin/blog/posts/create" class="px-4 py-2">Додати</a>
        </nav>
        <div class="card">
          <div class="card-body">
            <table class="table table-auto w-full">
              <thead>
              <tr>
                <th>#</th>
                <th>Автор</th>
                <th>Категорія</th>
                <th>Заголовок</th>
                <th>Дата публікації</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="post in posts" :key="post.id">
                <td>{{ post.id }}</td>
                <td>{{ post.user?.name }}</td>
                <td>{{ post.category?.title }}</td>
                <td>
                  <a :href="'/BlogPost/' + post.id" class="text-blue-400 hover:underline">
                    {{ post.title }}
                  </a>
                </td>
                <td>{{ post.published_at }}</td>
              </tr>
              </tbody>
            </table>
            <p v-if="error" class="text-red-500 mt-2">Помилка: {{ error }}</p>
            <p v-if="posts.length === 0 && !error" class="mt-2">Завантаження...</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Post {
  id: number
  title: string
  published_at: string | null
  user: { name: string }
  category: { title: string }
}

const posts = ref<Post[]>([])
const error = ref<string | null>(null)

const getPosts = () => {
  $fetch('http://127.0.0.1:8000/api/blog/posts')
    .then((response: any) => {
      console.log(response)
      posts.value = response.data
    })
    .catch((err) => {
      console.error(err)
      error.value = err.message ?? 'Невідома помилка'
    })
}

getPosts()
</script>
