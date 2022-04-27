import Request from '@/axios/request'

export function getCategories(params = {}) {
  return Request.get('system-media-categories', {
    params,
  })
}

export function destroyCategory(id) {
  return Request.delete(`system-media-categories/${id}`)
}

export function updateCategory(id, data) {
  return Request.put(`system-media-categories/${id}`, data)
}

export function storeCategory(data) {
  return Request.post('system-media-categories', data)
}

export function storeMedia(id, file) {
  const data = new FormData()
  data.append('file', file)

  return Request.post(`system-media-categories/${id}/system-media`, data)
}

export function getCategoryMedia(categoryId, params = {}) {
  return Request.get(`system-media-categories/${categoryId}/system-media`, {
    params,
  })
}

export function getMedia(params = {}) {
  return Request.get('system-media', {
    params,
  })
}

export function batchUpdateMedia(data) {
  return Request.put('system-media', data)
}

export function batchDestroyMedia(id) {
  return Request.post('system-media', {
    _method: 'DELETE',
    id,
  })
}
