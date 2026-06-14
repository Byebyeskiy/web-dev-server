<script setup>
useHead({ title: 'Checkout' })

const subscriptionStore = useSubscriptionStore()
const router = useRouter()

const { selectedPlan: plan, hasPlan } = storeToRefs(subscriptionStore)

onMounted(() => {
  if (!hasPlan.value) {
    router.push('/products')
  }
})

const form = reactive({
  cardNumber: '',
  expiry: '',
  cvc: '',
  fullName: '',
  address: '',
  consent: false
})

const isSubmitting = ref(false)
const submitError = ref('')
const submitSuccess = ref(false)

function formatCardNumber(e) {
  let value = e.target.value.replace(/\D/g, '')
  value = value.match(/.{1,4}/g)?.join(' ') || value
  form.cardNumber = value
}

function formatExpiry(e) {
  let value = e.target.value.replace(/\D/g, '')
  if (value.length >= 2) {
    value = value.slice(0, 2) + ' / ' + value.slice(2)
  }
  form.expiry = value
}

async function handleSubmit() {
  if (!form.consent) return

  isSubmitting.value = true
  submitError.value = ''
  submitSuccess.value = false

  try {
    await $fetch('/api/subscription/create', {
      method: 'POST',
      body: {
        fullName: form.fullName,
        address: form.address,
        cardNumber: form.cardNumber,
        expiry: form.expiry,
        cvc: form.cvc,
        planId: plan.value?.id
      }
    })
    subscriptionStore.clearPlan()
    await navigateTo('/table')
  }
  catch (err) {
    submitError.value = err?.data?.message || 'Something went wrong. Please try again.'
  }
  finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="checkout-page">
    <div class="checkout-header">
      <span class="checkout-title">Checkout</span>
    </div>

    <div class="checkout-body">
      <NuxtLink to="/products" class="back-link">&lt;&lt; back</NuxtLink>

      <h1 class="page-title">You're Almost In - Start Your 3-Day Free Trial Now!</h1>
      <p class="page-subtitle">
        Set up your account to gain instant access! You won't be charged if you decide to cancel within 3 days
      </p>

      <div class="checkout-grid">
        <div class="plan-card" v-if="plan">
          <div class="plan-accent-bar"></div>
          <h2 class="plan-name">{{ plan.name }}</h2>
          <p class="plan-trial-label">3-days free then:</p>
          <div class="plan-price">
            <span class="price-amount">${{ plan.monthlyPrice }}</span>
            <span class="price-period">/month</span>
          </div>
          <p class="plan-billing">
            billed yearly at
            <span class="price-original">${{ plan.originalYearly }}</span>
            ${{ plan.yearlyPrice }}
          </p>
          <div class="savings-badge">${{ plan.savings }} in savings</div>

          <ul class="features-list">
            <li v-for="feature in plan.features" :key="feature.text" class="feature-item">
              <span class="feature-icon">✦</span>
              <span>
                {{ feature.text }}
                <span v-if="feature.sub" class="feature-sub">({{ feature.sub }})</span>
              </span>
            </li>
          </ul>
        </div>

        <div class="order-section">
          <div class="order-summary">
            <h3 class="section-heading">Order Summary</h3>
            <div class="summary-row">
              <span>Annual Plan</span>
              <span>${{ plan?.yearlyPrice }}.00</span>
            </div>
            <div class="summary-row">
              <span>Total Due <small>(*not including sales tax where applicable)</small></span>
              <span>${{ plan?.yearlyPrice }}.00</span>
            </div>
            <div class="summary-row due-today">
              <span class="due-label">Due Today</span>
              <span class="due-amount">$0.00</span>
            </div>
            <div class="trial-badge">Includes 3-Day Free Trial</div>
          </div>

          <div class="billing-section">
            <h3 class="section-heading">
              Billing Information
              <span class="info-icon" title="Your billing info is secured with SSL encryption">ⓘ</span>
            </h3>

            <form class="billing-form" @submit.prevent="handleSubmit">
              <div class="form-group">
                <label class="form-label">Card Details</label>
                <div class="card-input-group">
                  <span class="card-icon">💳</span>
                  <input
                    v-model="form.cardNumber"
                    type="text"
                    placeholder="Number"
                    maxlength="19"
                    class="card-number-input"
                    @input="formatCardNumber"
                  />
                  <input
                    v-model="form.expiry"
                    type="text"
                    placeholder="MM / YY"
                    maxlength="7"
                    class="card-expiry-input"
                    @input="formatExpiry"
                  />
                  <input
                    v-model="form.cvc"
                    type="text"
                    placeholder="CVC"
                    maxlength="4"
                    class="card-cvc-input"
                  />
                </div>
              </div>

              <div class="form-group">
                <label class="form-label">Address</label>
                <input
                  v-model="form.fullName"
                  type="text"
                  placeholder="Full name"
                  class="form-input"
                />
                <textarea
                  v-model="form.address"
                  placeholder="Address"
                  rows="3"
                  class="form-input form-textarea"
                />
              </div>

              <div class="consent-row">
                <input
                  v-model="form.consent"
                  type="checkbox"
                  id="consent"
                  class="consent-checkbox"
                />
                <label for="consent" class="consent-label">
                  I consent to
                  <a href="#" class="terms-link">Terms of Use</a>
                  and understand my 3-day free trial will automatically convert to ${{ plan?.yearlyPrice }}.00 per year.
                  The yearly fee will be automatically charged each year going
                  forward unless I cancel my account at least one (1) business day before the end of
                  the current billing period, which can be done by calling (888) 463-3163.
                </label>
              </div>

              <button
                type="submit"
                class="submit-btn"
                :disabled="!form.consent || isSubmitting"
              >
                <span v-if="isSubmitting">Processing...</span>
                <span v-else>Try It Free</span>
              </button>

              <p v-if="submitError" class="error-message">{{ submitError }}</p>
              <p v-if="submitSuccess" class="success-message">Subscription created successfully!</p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.checkout-page {
  min-height: 100vh;
  background: #f7f8fa;
  font-family: 'Georgia', 'Times New Roman', serif;
}

.checkout-header {
  background: #1a1a1a;
  color: #fff;
  text-align: center;
  padding: 18px 24px;
}

.checkout-title {
  font-size: 1.1rem;
  font-weight: 600;
  letter-spacing: 0.04em;
  font-family: 'Helvetica Neue', sans-serif;
}

.checkout-body {
  max-width: 900px;
  margin: 0 auto;
  padding: 32px 24px 64px;
}

.back-link {
  color: #555;
  text-decoration: none;
  font-size: 0.9rem;
  font-family: 'Helvetica Neue', sans-serif;
  display: inline-block;
  margin-bottom: 20px;
  transition: color 0.2s;
}
.back-link:hover { color: #000; }

.page-title {
  font-size: 1.65rem;
  font-weight: 700;
  color: #111;
  margin-bottom: 10px;
  line-height: 1.3;
}

.page-subtitle {
  color: #555;
  font-size: 0.95rem;
  margin-bottom: 32px;
  font-family: 'Helvetica Neue', sans-serif;
}

.checkout-grid {
  display: grid;
  grid-template-columns: 300px 1fr;
  gap: 28px;
  align-items: start;
}
@media (max-width: 700px) {
  .checkout-grid { grid-template-columns: 1fr; }
}

.plan-card {
  background: #fff;
  border: 1px solid #e0e0e0;
  border-radius: 10px;
  padding: 28px 24px;
  position: relative;
  overflow: hidden;
}

.plan-accent-bar {
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 4px;
  background: linear-gradient(90deg, #4caf50, #26a69a);
}

.plan-name {
  font-size: 1.15rem;
  font-weight: 700;
  color: #111;
  margin: 4px 0 12px;
  font-family: 'Helvetica Neue', sans-serif;
}

.plan-trial-label {
  font-size: 0.8rem;
  color: #777;
  margin-bottom: 4px;
  font-family: 'Helvetica Neue', sans-serif;
}

.plan-price {
  display: flex;
  align-items: baseline;
  gap: 2px;
  margin-bottom: 6px;
}

.price-amount {
  font-size: 2.4rem;
  font-weight: 800;
  color: #111;
  line-height: 1;
}

.price-period {
  font-size: 0.9rem;
  color: #555;
  font-family: 'Helvetica Neue', sans-serif;
}

.plan-billing {
  font-size: 0.8rem;
  color: #777;
  margin-bottom: 10px;
  font-family: 'Helvetica Neue', sans-serif;
}

.price-original {
  text-decoration: line-through;
  color: #aaa;
}

.savings-badge {
  display: inline-block;
  background: #e8f5e9;
  color: #2e7d32;
  font-size: 0.78rem;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 20px;
  margin-bottom: 18px;
  font-family: 'Helvetica Neue', sans-serif;
}

.features-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.feature-item {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  font-size: 0.85rem;
  color: #333;
  font-family: 'Helvetica Neue', sans-serif;
  line-height: 1.4;
}

.feature-icon {
  color: #4caf50;
  font-size: 0.7rem;
  margin-top: 3px;
  flex-shrink: 0;
}

.feature-sub {
  color: #888;
  font-size: 0.78rem;
}

.order-section {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.order-summary {
  background: #fff;
  border: 1px solid #e0e0e0;
  border-radius: 10px;
  padding: 24px;
}

.section-heading {
  font-size: 1rem;
  font-weight: 700;
  color: #111;
  margin-bottom: 16px;
  font-family: 'Helvetica Neue', sans-serif;
  display: flex;
  align-items: center;
  gap: 6px;
}

.info-icon {
  font-size: 0.8rem;
  color: #888;
  cursor: help;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
  border-bottom: 1px solid #f0f0f0;
  font-size: 0.88rem;
  color: #333;
  font-family: 'Helvetica Neue', sans-serif;
  gap: 12px;
}

.summary-row small {
  font-size: 0.75rem;
  color: #999;
}

.due-today {
  border-bottom: none;
  padding-top: 14px;
  margin-top: 4px;
}

.due-label {
  font-weight: 700;
  font-size: 0.95rem;
  color: #111;
}

.due-amount {
  font-weight: 700;
  font-size: 0.95rem;
  color: #111;
}

.trial-badge {
  margin-top: 14px;
  background: #f5f5f5;
  border-radius: 6px;
  text-align: center;
  padding: 10px;
  font-size: 0.85rem;
  color: #555;
  font-family: 'Helvetica Neue', sans-serif;
}

.billing-section {
  background: #fff;
  border: 1px solid #e0e0e0;
  border-radius: 10px;
  padding: 24px;
}

.billing-form {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-label {
  font-size: 0.85rem;
  font-weight: 600;
  color: #333;
  font-family: 'Helvetica Neue', sans-serif;
}

.card-input-group {
  display: flex;
  align-items: center;
  border: 1px solid #d0d0d0;
  border-radius: 6px;
  overflow: hidden;
  background: #fff;
}

.card-icon {
  padding: 0 10px;
  font-size: 1.1rem;
  flex-shrink: 0;
}

.card-number-input {
  flex: 1;
  border: none;
  outline: none;
  padding: 11px 8px;
  font-size: 0.9rem;
  font-family: 'Helvetica Neue', sans-serif;
  color: #000000;
}

.card-expiry-input,
.card-cvc-input {
  border: none;
  border-left: 1px solid #e0e0e0;
  outline: none;
  padding: 11px 10px;
  font-size: 0.9rem;
  width: 80px;
  font-family: 'Helvetica Neue', sans-serif;
  text-align: center;
  color: #000000;
}

.form-input {
  width: 100%;
  border: 1px solid #d0d0d0;
  border-radius: 6px;
  padding: 11px 14px;
  font-size: 0.9rem;
  font-family: 'Helvetica Neue', sans-serif;
  outline: none;
  transition: border-color 0.2s;
  box-sizing: border-box;
  color: #000000;
}

.form-input:focus {
  border-color: #4caf50;
}

.form-textarea {
  resize: vertical;
  min-height: 72px;
}

.consent-row {
  display: flex;
  align-items: flex-start;
  gap: 10px;
}

.consent-checkbox {
  margin-top: 3px;
  flex-shrink: 0;
  width: 15px;
  height: 15px;
  cursor: pointer;
}

.consent-label {
  font-size: 0.78rem;
  color: #555;
  line-height: 1.5;
  font-family: 'Helvetica Neue', sans-serif;
}

.terms-link {
  color: #1565c0;
  text-decoration: underline;
}

.submit-btn {
  background: #d0d0d0;
  color: #555;
  border: none;
  border-radius: 6px;
  padding: 13px 24px;
  font-size: 0.95rem;
  font-weight: 600;
  font-family: 'Helvetica Neue', sans-serif;
  transition: background 0.2s, color 0.2s;
  align-self: flex-start;
  cursor: not-allowed;
}

.submit-btn:not(:disabled) {
  background: #4caf50;
  color: #fff;
  cursor: pointer;
}

.submit-btn:not(:disabled):hover {
  background: #388e3c;
}

.error-message {
  color: #c62828;
  font-size: 0.85rem;
  font-family: 'Helvetica Neue', sans-serif;
}

.success-message {
  color: #2e7d32;
  font-size: 0.85rem;
  font-family: 'Helvetica Neue', sans-serif;
}
</style>
