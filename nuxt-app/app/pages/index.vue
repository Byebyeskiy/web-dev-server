<template>
  <div class="page">
    <div class="cta-block">
      <NuxtLink to="/products" class="cta-btn">
        Переглянути продукти →
      </NuxtLink>
    </div>
  </div>
</template>

<style scoped>
.page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f7f8fa;
}

.cta-btn {
  background: #4caf50;
  color: #fff;
  border-radius: 8px;
  padding: 14px 32px;
  font-size: 1rem;
  font-weight: 600;
  text-decoration: none;
  font-family: 'Helvetica Neue', sans-serif;
  transition: background 0.2s;
}

.cta-btn:hover {
  background: #388e3c;
}
</style>
