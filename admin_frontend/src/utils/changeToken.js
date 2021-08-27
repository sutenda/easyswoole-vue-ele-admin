
export function changeToken(val) {
  return parseInt(val) / 100
}

export function deepCopy(obj) {
  const data = JSON.stringify(obj)
  return JSON.parse(data)
}
