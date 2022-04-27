import { getExt } from '@/libs/utils'
import { IMAGE_EXTS } from '@/libs/constants'


export function isExternal(path) {
  return /^(https?:|mailto:|tel:)/.test(path)
}


export function isInt(val) {
  return /^[+-]?\d+$/.test(val)
}


export function isImage(file, isExt = false) {
  let ext

  if (isExt) {
    ext = file
  } else if (file instanceof File) {
    ext = getExt(file.name)
  } else {
    ext = getExt(file)
  }

  return IMAGE_EXTS.indexOf(ext.toLowerCase()) !== -1
}
