const Meta = {
  name: 'Activities',
  icon: 'stop_circle',
  module: 'activities',
  topBarMenu: [],
  permission: {
    browse: true,
    create: true,
    update: true,
    delete: true,
    restore: true
  },
  model: {
    id: null,
    reference: null,
    reference_id: null,
    title: null,
    description: null,
    due: null,
    created_by: null,
    updated_by: null,
    deleted_by: null,
    file: []
  }
}

export default Meta
