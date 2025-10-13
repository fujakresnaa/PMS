const Meta = {
  name: 'Work Assignees',
  icon: 'stop_circle',
  module: 'work-assignees',
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
    work_id: null,
    user_id: null,
    created_by: null,
    updated_by: null,
    deleted_by: null

  }
}

export default Meta
