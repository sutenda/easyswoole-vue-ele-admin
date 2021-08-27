import request from '@/utils/request'

// 查询管理员
export function getAdmins() {
  return request({
    url: '/user/getAll',
    method: 'get'
  })
}

// 新增角色组
export function addAdmin(data) {
  return request({
    url: '/user/add',
    method: 'post',
    data
  })
}

// 更新角色组
export function updateAdmin(id, data) {
  return request({
    url: '/user/update?id=' + id,
    method: 'post',
    data
  })
}

// 删除角色组
export function deleteAdmin(id) {
  return request({
    url: '/user/delete?id=' + id,
    method: 'post',
    isForm: true
  })
}

// 批量删除
export function multiDeleteAdmins(data) {
  return request({
    url: '/user/multiDelete',
    method: 'post',
    data
  })
}
