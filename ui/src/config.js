import {
  LocalStorage,
  Dialog
} from 'quasar'
import { Helper } from './services/helper'

export const Config = {
  test () {
    console.log('Config berhasil di panggil')
  },

  appName () {
    return 'Project Management System'
  },

  version () {
    return '1.0.4'
  },

  setApiRoot () {
    var api = Config.getApiRoot()
    Dialog.create({
      title: 'Set API Root',
      message: 'ex: http://localhost/',
      prompt: {
        model: api,
        type: 'text' // optional
      },
      cancel: true,
      persistent: true
    }).onOk(data => {
      var val = data
      const last = val.charAt(val.length - 1)
      let endpoint = val
      if (last !== '/') endpoint = endpoint + '/'

      Config.saveApiRoot(endpoint)
      window.location.reload()
    }).onCancel(() => {
      // console.log('>>>> Cancel')
    }).onDismiss(() => {
      // console.log('I am triggered on both OK and Cancel')
    })
  },

  setApiTemp () {
    var api = Config.getApiTemp()
    Dialog.create({
      title: 'Set BMS Service url',
      message: 'ex: http://192.168.43.227/bms_service/',
      prompt: {
        model: api,
        type: 'text' // optional
      },
      cancel: true,
      persistent: true
    }).onOk(data => {
      var val = data
      const last = val.charAt(val.length - 1)
      let endpoint = val
      if (last !== '/') endpoint = endpoint + '/'

      Config.saveApiTemp(endpoint)
    }).onCancel(() => {
      // console.log('>>>> Cancel')
    }).onDismiss(() => {
      // console.log('I am triggered on both OK and Cancel')
    })
  },

  getApiRoot () {
    if (LocalStorage.has('apiroot') === false) {
      var url = 'http://bms-api.sopeus.com/'
      var icon = '<i aria-hidden="true" role="img" class="material-icons q-icon notranslate text-primary">settings</i>'
      Helper.showAlert('API Root Not Defined', 'Api Root aplikasi belum diatur di perangkat ini, sistem otomatis mengarahkan Api Root ke :  <br> <span class="text-primary">' + url + ' </span> <br> Klik icon roda gigi (' + icon + ') di kiri atas form login untuk mengatur Api Root. ')
      LocalStorage.set('apiroot', url)
    }
    return LocalStorage.getItem('apiroot')
  },

  saveApiRoot (val) {
    LocalStorage.set('apiroot', val)
  },

  saveApiTemp (val) {
    LocalStorage.set('apitmp', val)
  },

  getMsgCofirm (type) {
    if (type === 'delete') return 'Yakin akan menghapus data ?'
    else return 'Haloo..'
  },

  labelSearchProduk () {
    return 'Ketik 3 digit nama produk untuk mencari, gunakan awalan (:) untuk cari by kode & (~) untuk cari by alias ..'
  },

  currencyConfig () {
    var d = {
      decimal: ',',
      thousands: '.',
      prefix: '',
      suffix: '',
      precision: 2,
      masked: false,
      max: 16
    }
    return d
  },

  numberOnly () {
    var d = {
      decimal: ',',
      thousands: '.',
      prefix: '',
      suffix: '',
      precision: 0,
      masked: false,
      max: 16
    }
    return d
  },

  // credentials method
  credentials (saving) {
    if (saving === 'destroy') {
      const apiroot = this.getApiRoot()

      localStorage.clear()
      this.saveApiRoot(apiroot)
    } else if (saving !== undefined) {
      console.log('saving cre', saving)
      if (saving.token !== undefined) Helper.saveLdb('token', saving.token)
      // saving.token = ''
      // saving.remember_token = ''
      Helper.saveLdb('credentials', saving)
      return
    }

    // retreive data
    if (Helper.checkLdb('credentials')) {
      var credentials = Helper.getLdb('credentials')
      return credentials
    } else return false
  },

  scrollOptions (type) {
    // var thumbStyle = {
    //   right: '4px',
    //   borderRadius: '5px',
    //   backgroundColor: '#424242',
    //   width: '7px',
    //   opacity: 0.75
    // }
    // var barStyle = {
    //   right: '2px',
    //   borderRadius: '9px',
    //   backgroundColor: '#424242',
    //   width: '11px',
    //   opacity: 0.2
    // }

    // if (type === 'bar') return barStyle
    // else return thumbStyle
    return null
  },

  editorTollbar ($q) {
    var data = [
      [
        {
          label: $q.lang.editor.align,
          icon: $q.iconSet.editor.align,
          fixedLabel: true,
          list: 'only-icons',
          options: ['left', 'center', 'right', 'justify']
        },
        {
          label: $q.lang.editor.align,
          icon: $q.iconSet.editor.align,
          fixedLabel: true,
          options: ['left', 'center', 'right', 'justify']
        }
      ],
      ['bold', 'italic', 'strike', 'underline', 'subscript', 'superscript'],
      ['token', 'hr', 'link', 'custom_btn'],
      ['print', 'fullscreen'],
      [
        {
          label: $q.lang.editor.formatting,
          icon: $q.iconSet.editor.formatting,
          list: 'no-icons',
          options: [
            'p',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'code'
          ]
        },
        {
          label: $q.lang.editor.fontSize,
          icon: $q.iconSet.editor.fontSize,
          fixedLabel: true,
          fixedIcon: true,
          list: 'no-icons',
          options: [
            'size-1',
            'size-2',
            'size-3',
            'size-4',
            'size-5',
            'size-6',
            'size-7'
          ]
        },
        {
          label: $q.lang.editor.defaultFont,
          icon: $q.iconSet.editor.font,
          fixedIcon: true,
          list: 'no-icons',
          options: [
            'default_font',
            'arial',
            'arial_black',
            'comic_sans',
            'courier_new',
            'impact',
            'lucida_grande',
            'times_new_roman',
            'verdana'
          ]
        },
        'removeFormat'
      ],
      ['quote', 'unordered', 'ordered', 'outdent', 'indent'],

      ['undo', 'redo'],
      ['viewsource']
    ]

    return data
  },

  editorFont () {
    var data = {
      arial: 'Arial',
      arial_black: 'Arial Black',
      comic_sans: 'Comic Sans MS',
      courier_new: 'Courier New',
      impact: 'Impact',
      lucida_grande: 'Lucida Grande',
      times_new_roman: 'Times New Roman',
      verdana: 'Verdana'
    }

    return data
  },

  iconPriority (val) {
    if (val === 'REGULAR') return 'beenhere'
    if (val === 'LOW') return 'local_cafe'
    if (val === 'HIGH') return 'warning'
    if (val === 'URGENT') return 'run_circle'
  },

  colorPriority (val) {
    if (val === 'REGULAR') return 'primary'
    if (val === 'LOW') return 'blue'
    if (val === 'HIGH') return 'orange'
    if (val === 'URGENT') return 'red'
  },

  iconStatusWork (val) {
    if (val === 'OPEN') return 'local_offer'
    if (val === 'RUNNING') return 'directions_bike'
    if (val === 'COMPLETED') return 'assignment_turned_in'
  },

  colorStatusWork (val) {
    if (val === 'OPEN') return 'blue'
    if (val === 'RUNNING') return 'purple'
    if (val === 'COMPLETED') return 'green'
  },

  iconStatusSprints (val) {
    if (val === 'OPEN') return 'local_offer'
    if (val === 'STARTED') return 'directions_bike'
    if (val === 'FINISHED') return 'assignment_turned_in'
  },

  colorStatusSprints (val) {
    if (val === 'OPEN') return 'blue'
    if (val === 'STARTED') return 'purple'
    if (val === 'FINISHED') return 'green'
  },

  iconComponent (val) {
    if (val === 'DOC') return 'menu_book'
    if (val === 'DOC-API') return 'menu_book'
    if (val === 'SQL') return 'storage'
    if (val === 'RULE') return 'rule'
    if (val === 'NOTE') return 'sticky_note_2'
    if (val === 'OTHER') return 'bookmarks'
  },

  colorComponent (val) {
    if (val === 'DOC') return 'blue'
    if (val === 'DOC-API') return 'orange-8'
    if (val === 'SQL') return 'red'
    if (val === 'RULE') return 'purple'
    if (val === 'NOTE') return 'green'
    if (val === 'OTHER') return 'grey-8'
  }
}
