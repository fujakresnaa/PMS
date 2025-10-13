# Patria Maritim Perkasa (pmp)

Patria Maritim Perkasa - Enterprise Resource Planning & Finance Service Microsoft Business Service

### Install the dependencies if firs pull this project
```bash
npm install
```

### Start the app in development mode (hot-code reloading, error reporting, etc.)
```bash
quasar dev
```

### Lint the files
```bash
npm run lint
```

### Build the app for production
```bash
quasar build
```

### Customize the configuration
See [Configuring quasar.conf.js](https://quasar.dev/quasar-cli/quasar-conf-js).


---

1 Project bisa punya banyak Members
1 Project bisa punya banyak Works
1 Works Bisa punya banyak Sprints
1 Sprints punya banyak Tasks

Project: PMP
Works : [PMP]
 - BRD
 - Init Master Module
 
Sprint : [Init Master Module]
 - SPRINT_init_master_module
 - SPRINT_init_master_module_2

Tasks : [SPRINT_init_master_module]
 - Users
 - Roles

Tasks : [SPRINT_init_master_module_2]
 - Menus
 - Permissions

# Rules
 - TaskStatus : tiap2 sprint memiliki status yg berbeda

# Kondisi
- Jika Sprint di started & work atas sprint ber status "OPEN" , 
  statusnya diubah jadi = RUNNING

