import { reactive } from 'vue'

export const notifications = reactive([])

let notificationId = 0

export function pushNotification(message, type = 'success') {
  const id = notificationId++

  notifications.push({ id, message, type })

  window.setTimeout(() => {
    removeNotification(id)
  }, 4500)
}

export function removeNotification(id) {
  const index = notifications.findIndex((notification) => notification.id === id)

  if (index !== -1) {
    notifications.splice(index, 1)
  }
}
