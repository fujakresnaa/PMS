const Meta = {
  name: 'Tasks',
  icon: 'stop_circle',
  module: 'tasks',
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
    project_id: null,
    sprint_id: null,
    work_id: null,
    status: null,
    name: null,
    description: '',
    start_date: null,
    end_date: null,
    actual_start_date: null,
    actual_end_date: null,
    labels: null,
    ordering: 1,
    mandays: 0,
    mandays_actual: 0,
    is_overtime: false,
    is_done: false,
    created_by: null,
    updated_by: null,
    deleted_by: null,
    progress: 0,
    assignees: []

  }
}

export default Meta
