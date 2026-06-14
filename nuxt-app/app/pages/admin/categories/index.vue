<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-2xl font-bold">Категорії блогу</h1>
      <UButton label="Додати категорію" icon="i-lucide-plus" to="/admin/categories/create" />
    </div>

    <UTable :data="categories" :columns="columns" :loading="loading">
      <template #actions-cell="{ row }">
        <UDropdownMenu :items="getActions(row.original)">
          <UButton icon="i-lucide-ellipsis" variant="ghost" />
        </UDropdownMenu>
      </template>
    </UTable>
  </div>
</template>

<script setup lang="ts">
interface Category {
  id: number
  title: string
  slug: string
}

const categories = ref<Category[]>([])
const loading = ref(false)

const columns = [
  { accessorKey: 'id', header: '#' },
  { accessorKey: 'title', header: 'Назва' },
  { accessorKey: 'slug', header: 'Slug' },
  { id: 'actions', header: 'Дії' }
]

const getActions = (category: Category) => [
  [{
    label: 'Редагувати',
    icon: 'i-lucide-pencil',
    onSelect: () => navigateTo(`/admin/categories/${category.id}/edit`)
  }],
  [{
    label: 'Видалити',
    icon: 'i-lucide-trash',
    color: 'error' as const,
    onSelect: () => deleteCategory(category.id)
  }]
]

const getCategories = async () => {
  loading.value = true
  try {
    const response: any = await $fetch('http://127.0.0.1:8000/api/blog/admin/categories')
    categories.value = response.data
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

const deleteCategory = async (id: number) => {
  if (!confirm('Видалити категорію?')) return
  try {
    await $fetch(`http://127.0.0.1:8000/api/blog/admin/categories/${id}`, { method: 'DELETE' })
    await getCategories()
  } catch (err) {
    console.error(err)
  }
}

getCategories()
</script>
