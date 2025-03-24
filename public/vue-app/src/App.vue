<template>
  <div id="app">
    <header>
      <a href="https://vite.dev" target="_blank">
        <img src="/vite.svg" class="logo" alt="Vite logo" />
      </a>
      <a href="https://vuejs.org/" target="_blank">
        <img src="./assets/vue.svg" class="logo vue" alt="Vue logo" />
      </a>
    </header>
    <nav>
      <ul>
        <li><router-link to="/users">Người Dùng</router-link></li>
        <li><router-link to="/vat-invoices">Hóa Đơn VAT</router-link></li>
        <li v-if="!isAuthenticated"><router-link to="/login">Đăng Nhập</router-link></li>
        <li v-if="!isAuthenticated"><router-link to="/register">Đăng Kí</router-link></li>
        <li v-if="isAuthenticated"><router-link to="/add-vat-invoice">Thêm hóa đơn</router-link></li>
        <li v-if="isAuthenticated"><button @click="logout">Đăng Xuất</button></li>
      </ul>
    </nav>
    <main>
      <router-view />
    </main>
  </div>
</template>
<script>
export default {
  data() {
    return {
      isAuthenticated: false,
    };
  },
  mounted() {
    // Kiểm tra sự tồn tại của token khi component được gắn vào
    const token = localStorage.getItem("token");
    this.isAuthenticated = !!token; // Nếu có token, isAuthenticated sẽ là true
  },
  methods: {
    logout() {
      localStorage.removeItem("token");
      localStorage.removeItem("customer_id");
      this.isAuthenticated = false; // Cập nhật trạng thái
      this.$router.push("/login");
    },
  },
};
</script>
<style scoped>
header {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin-bottom: 20px;
}
nav ul {
  list-style: none;
  display: flex;
  gap: 15px;
  justify-content: center;
  padding: 0;
  margin-bottom: 20px;
}
nav ul li {
  cursor: pointer;
  font-size: 18px;
}
main {
  text-align: center;
  padding: 20px;
}
</style>
