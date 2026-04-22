export function getApiMessage(error, fallback = 'Nao foi possivel concluir a operacao.') {
  return error?.response?.data?.message || fallback
}

export function getValidationErrors(error) {
  if (error?.response?.status !== 422) {
    return {}
  }

  return error.response.data.errors || {}
}
