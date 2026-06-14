<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-2xl font-bold">Пости блогу (Admin)</h1>
      <UButton label="Додати пост" icon="i-lucide-plus" to="/admin/posts/create" />
    </div>
    <UTable :data="posts" :columns="columns" :loading="loading">
      <template #actions-cell="{ row }">
        <UDropdownMenu :items="getActions(row.original)">
          <UButton icon="i-lucide-ellipsis" variant="ghost" />
        </UDropdownMenu>
      </template>
    </UTable>
    <div class="flex justify-center mt-4">
      <UPagination v-model:page="page" :total="total" :items-per-page="perPage" />
    </div>
  </div>
</template>

<script setup lang="ts">
interface Post {
  id: number
  title: string
  author_name: string
  category_title: string
  date_published: string | null
  is_published: boolean
}

const posts = ref<Post[]>([])
const loading = ref(false)
const page = ref(1)
const perPage = ref(25)
const total = ref(0)

const columns = [
  { accessorKey: 'id', header: '#' },
  { accessorKey: 'author_name', header: 'Автор' },
  { accessorKey: 'category_title', header: 'Категорія' },
  { accessorKey: 'title', header: 'Заголовок' },
  { accessorKey: 'date_published', header: 'Дата' },
  { id: 'actions', header: 'Дії' }
]

const getActions = (post: Post) => [
  [{
    label: 'Редагувати',
    icon: 'i-lucide-pencil',
    onSelect: () => navigateTo(`/admin/posts/${post.id}/edit`)
  }],
  [{
    label: 'Видалити',
    icon: 'i-lucide-trash',
    color: 'error' as const,
    onSelect: () => deletePost(post.id)
  }]
]

const getPosts = async () => {
  loading.value = true
  try {
    const response: any = await $fetch('http://127.0.0.1:8000/api/blog/posts', {
      params: { page: page.value }
    })
    posts.value = response.data
    total.value = response.meta?.total ?? 0
    perPage.value = response.meta?.per_page ?? 25
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

const deletePost = async (id: number) => {
  if (!confirm('Видалити пост?')) return
  try {
    await $fetch(`http://127.0.0.1:8000/api/blog/posts/${id}`, { method: 'DELETE' })
    await getPosts()
  } catch (err) {
    console.error(err)
  }
}

watch(page, getPosts)
getPosts()
</script>
