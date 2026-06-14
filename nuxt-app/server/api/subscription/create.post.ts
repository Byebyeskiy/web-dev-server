export default defineEventHandler(async (event) => {
  const body = await readBody(event)

  if (!body.fullName || !body.cardNumber || !body.expiry || !body.cvc) {
    throw createError({
      statusCode: 400,
      data: { message: 'Please fill in all required billing fields.' }
    })
  }

  return {
    success: true,
    message: 'Subscription created successfully.'
  }
})
