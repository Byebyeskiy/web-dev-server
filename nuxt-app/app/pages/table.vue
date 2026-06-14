<script setup>
useHead({ title: 'Таблиця' })

const search = ref('')
const page = ref(1)
const pageSize = 10

const { data, pending, error } = await useFetch('https://dummyjson.com/products?limit=100')

const products = computed(() => data.value?.products ?? [])

const sorting = ref({ column: null, direction: 'asc' })

function toggleSort(col) {
  if (sorting.value.column === col) {
    sorting.value.direction = sorting.value.direction === 'asc' ? 'desc' : 'asc'
  } else {
    sorting.value.column = col
    sorting.value.direction = 'asc'
  }
  page.value = 1
}

const filteredAndSorted = computed(() => {
  let items = [...products.value]

  if (search.value.trim()) {
    const q = search.value.toLowerCase()
    items = items.filter(p =>
      p.title?.toLowerCase().includes(q) ||
      p.description?.toLowerCase().includes(q) ||
      p.brand?.toLowerCase().includes(q) ||
      p.category?.toLowerCase().includes(q)
    )
  }

  if (sorting.value.column) {
    const col = sorting.value.column
    const dir = sorting.value.direction === 'asc' ? 1 : -1
    items.sort((a, b) => {
      const av = a[col] ?? ''
      const bv = b[col] ?? ''
      if (typeof av === 'number' && typeof bv === 'number') return (av - bv) * dir
      return String(av).localeCompare(String(bv)) * dir
    })
  }

  return items
})

const totalPages = computed(() => Math.ceil(filteredAndSorted.value.length / pageSize))

const paginated = computed(() => {
  const start = (page.value - 1) * pageSize
  return filteredAndSorted.value.slice(start, start + pageSize)
})

watch(search, () => { page.value = 1 })

const columns = [
  { key: 'title', label: 'Назва' },
  { key: 'description', label: 'Опис' },
  { key: 'price', label: 'Ціна' },
  { key: 'rating', label: 'Оцінка' },
  { key: 'brand', label: 'Бренд' },
  { key: 'category', label: 'Категорія' },
  { key: 'thumbnail', label: 'Фото' },
]
</script>

<template>
  <div class="table-page">
    <div class="table-header">
      <h1 class="table-title">Таблиця продуктів</h1>
    </div>

    <div class="table-controls">
      <input
        v-model="search"
        class="search-input"
        placeholder="Пошук..."
      />
    </div>

    <div v-if="pending" class="loading">Завантаження...</div>
    <div v-else-if="error" class="error-msg">Помилка завантаження даних.</div>

    <div v-else class="table-wrapper">
      <table class="products-table">
        <thead>
        <tr>
          <th
            v-for="col in columns"
            :key="col.key"
            @click="col.key !== 'thumbnail' && col.key !== 'description' ? toggleSort(col.key) : null"
            :class="{ sortable: col.key !== 'thumbnail' && col.key !== 'description' }"
          >
            {{ col.label }}
            <span v-if="sorting.column === col.key">
                {{ sorting.direction === 'asc' ? '↑' : '↓' }}
              </span>
          </th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="product in paginated" :key="product.id">
          <td class="td-title">{{ product.title }}</td>
          <td class="td-desc">{{ product.description }}</td>
          <td class="td-price">${{ product.price }}</td>
          <td
            class="td-rating"
            :class="product.rating >= 4.5 ? 'rating-good' : 'rating-bad'"
          >
            {{ product.rating }}
          </td>
          <td>{{ product.brand }}</td>
          <td>{{ product.category }}</td>
          <td>
            <img :src="product.thumbnail" :alt="product.title" class="thumb" />
          </td>
        </tr>
        </tbody>
      </table>
    </div>

    <div class="pagination" v-if="totalPages > 1">
      <button
        class="page-btn"
        :disabled="page === 1"
        @click="page--"
      >‹</button>

      <button
        v-for="p in totalPages"
        :key="p"
        class="page-btn"
        :class="{ active: p === page }"
        @click="page = p"
      >{{ p }}</button>

      <button
        class="page-btn"
        :disabled="page === totalPages"
        @click="page++"
      >›</button>
    </div>
  </div>
</template>

<style scoped>
.table-page {
  min-height: 100vh;
  background: #f7f8fa;
  padding: 48px 24px;
  font-family: 'Helvetica Neue', sans-serif;
}

.table-header {
  text-align: center;
  margin-bottom: 32px;
}

.table-title {
  font-size: 2rem;
  font-weight: 800;
  color: #111;
}

.table-controls {
  max-width: 1200px;
  margin: 0 auto 20px;
}

.search-input {
  width: 100%;
  padding: 10px 16px;
  border: 1px solid #d0d0d0;
  border-radius: 8px;
  font-size: 0.95rem;
  outline: none;
  box-sizing: border-box;
  transition: border-color 0.2s;
  color: black;
}

.search-input:focus {
  border-color: #4caf50;
}

.loading {
  text-align: center;
  color: #888;
  padding: 40px;
}

.error-msg {
  text-align: center;
  color: #c62828;
  padding: 40px;
}

.table-wrapper {
  max-width: 1200px;
  margin: 0 auto;
  overflow-x: auto;
  background: #fff;
  border-radius: 12px;
  border: 1px solid #e0e0e0;
}

.products-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.88rem;
}

.products-table thead tr {
  background: #f0f0f0;
}

.products-table th {
  padding: 12px 16px;
  text-align: left;
  font-weight: 700;
  color: #333;
  border-bottom: 2px solid #e0e0e0;
  white-space: nowrap;
  user-select: none;
}

.products-table th.sortable {
  cursor: pointer;
}

.products-table th.sortable:hover {
  background: #e8f5e9;
  color: #2e7d32;
}

.products-table td {
  padding: 12px 16px;
  border-bottom: 1px solid #f0f0f0;
  vertical-align: middle;
  color: #333;
}

.products-table tbody tr:last-child td {
  border-bottom: none;
}

.products-table tbody tr:hover {
  background: #fafafa;
}

.td-title {
  font-weight: 600;
  min-width: 140px;
}

.td-desc {
  max-width: 220px;
  color: #666;
  font-size: 0.82rem;
  line-height: 1.4;
}

.td-price {
  font-weight: 700;
  color: #111;
  white-space: nowrap;
}

.td-rating {
  font-weight: 700;
  white-space: nowrap;
}

.rating-good {
  color: #2e7d32;
}

.rating-bad {
  color: #c62828;
}

.thumb {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 6px;
}

.pagination {
  display: flex;
  justify-content: center;
  gap: 6px;
  margin-top: 24px;
  flex-wrap: wrap;
  max-width: 1200px;
  margin-left: auto;
  margin-right: auto;

}

.page-btn {
  min-width: 36px;
  height: 36px;
  padding: 0 10px;
  border: 1px solid #d0d0d0;
  border-radius: 6px;
  background: #fff;
  color: #333;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s;
}

.page-btn:hover:not(:disabled) {
  border-color: #4caf50;
  color: #4caf50;
}

.page-btn.active {
  background: #4caf50;
  color: #fff;
  border-color: #4caf50;
  font-weight: 700;
}

.page-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
</style>
