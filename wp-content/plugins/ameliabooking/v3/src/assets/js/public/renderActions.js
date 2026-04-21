function useRenderAction (action, data) {
  if ('ameliaRenderActions' in window && action in window.ameliaRenderActions) {
    window.ameliaRenderActions[action](data)
  }
}

export { useRenderAction }
