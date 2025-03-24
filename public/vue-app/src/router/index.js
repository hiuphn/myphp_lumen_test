import { createRouter, createWebHistory } from 'vue-router';
import UserList from '../components/UserList.vue';
import Vat_InvoiceList from '../components/Vat_InvoiceList.vue';
import Login from '../components/Login.vue';
import Register from '../components/Register.vue';
import AddVatInvoice from '../components/AddVatInvoice.vue';

const routes = [
  { path: '/', redirect: '' },
  { path: '/users', component: UserList, name: 'UserList' },
  { path: '/vat-invoices', component: Vat_InvoiceList, name: 'VatInvoiceList' },
  { path: '/login', component: Login, name: 'Login' },
  { path: '/register', component: Register, name: 'Register' },
  { path: '/add-vat-invoice', component: AddVatInvoice, name: 'AddInvoice' },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
