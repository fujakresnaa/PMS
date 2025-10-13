# UI Rules : 
Pastikan module file dibuat menggunakan generator agar konsisten


-----


## File Name :
Untuk foldering module gunakan kebab-case,
contoh module `Master Customer` berarti folder namenya `master-customer`
dan pada tiap-tiap isian file wajib mengikuti default

- Form Create / Update : form [bila perlu dipisah bisa disesuaikan]
- Detail / View : view 
- Index / List : index 


-----


## UI Route :
- name : sesuaikan dengan nama module dengan menggunakan kebab-case,
contoh module `Master Customer` berarti route namenya `master-customer`
  - gunakan awalan postfix `-list` untuk list page
  - gunakan awalan prefix `view-` untuk detail page
  - gunakan awalan prefix `add-` untuk form create page
  - gunakan awalan prefix `edit-` untuk form update page

- path :
  - untuk list data page gunakan full name module saja
  - untuk detail page`{module-name}/view/:id`
  - untuk create page `{module-name}/form`
  - untuk update page `{module-name}/form/:id`

notes : 
- bila ada modul khusus, harap di sesuaikan dengan master menu
- pastikan `route name` sesuai dengan `slug` di menu items


-----

