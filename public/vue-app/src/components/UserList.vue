<!-- src/components/UserManagement.vue -->
<template>
  <div class="container">
    <h2>Quản lý Người Dùng</h2>

    <!-- Nút đăng xuất -->
    <div class="logout">
      <button @click="logout">Đăng Xuất</button>
    </div>

    <!-- Thông báo lỗi -->
    <div v-if="errorMessage" class="error-message">
      {{ errorMessage }}
    </div>

    <!-- Form thêm người dùng -->
    <div class="form-group">
      <input v-model="newUser.name" placeholder="Tên" required />
      <input v-model="newUser.email" placeholder="Email" type="email" required />
      <input v-model="newUser.password" placeholder="Password" type="password" required />
      <button @click="addUser" :disabled="isLoading">Thêm</button>
    </div>

    <!-- Trạng thái loading -->
    <div v-if="isLoading" class="loading">Đang xử lý...</div>

    <!-- Danh sách người dùng -->
    <h3>Danh sách Người Dùng</h3>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Tên</th>
          <th>Email</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td>
            <button @click="viewUser(user.id)" :disabled="isLoading">Xem</button>
            <!-- <button @click="editUser(user.id)" :disabled="isLoading">Sửa</button> -->
            <button @click="deleteUser(user.id)" :disabled="isLoading">Xóa</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      users: [],
      newUser: { name: "", email: "", password: "" },
      API_URL: "http://localhost:8000/users",
      errorMessage: "",
      isLoading: false,
    };
  },
  methods: {
    // Kiểm tra token và chuyển hướng nếu chưa đăng nhập
    checkAuth() {
      const token = localStorage.getItem("token");
      if (!token) {
        alert("Bạn cần đăng nhập để tiếp tục!");
        this.$router.push("/login");
        return false;
      }
      return true;
    },

    // Thêm token vào header của axios
    getAxiosConfig() {
      const token = localStorage.getItem("token");
      return {
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
      };
    },

    // Xóa thông báo lỗi
    clearError() {
      this.errorMessage = "";
    },

    // Lấy danh sách người dùng
    async fetchUsers() {
      if (!this.checkAuth()) return;

      this.isLoading = true;
      this.clearError();
      try {
        const response = await axios.get(this.API_URL, this.getAxiosConfig());
        this.users = response.data;
      } catch (error) {
        this.handleError(error, "Lỗi khi tải danh sách người dùng");
      } finally {
        this.isLoading = false;
      }
    },

    // Thêm người dùng mới
    async addUser() {
      if (!this.checkAuth()) return;

      if (!this.newUser.name || !this.newUser.email || !this.newUser.password) {
        this.errorMessage = "Vui lòng điền đầy đủ thông tin (Tên, Email, Password).";
        return;
      }

      this.isLoading = true;
      this.clearError();
      try {
        await axios.post(this.API_URL, this.newUser, this.getAxiosConfig());
        alert("Thêm người dùng thành công!");
        this.newUser = { name: "", email: "", password: "" };
        await this.fetchUsers();
      } catch (error) {
        this.handleError(error, "Lỗi khi thêm người dùng");
      } finally {
        this.isLoading = false;
      }
    },

    // Xem thông tin người dùng
    async viewUser(id) {
      if (!this.checkAuth()) return;

      this.isLoading = true;
      this.clearError();
      try {
        const response = await axios.get(
          `${this.API_URL}/${id}`,
          this.getAxiosConfig()
        );
        const user = response.data;
        alert(`ID: ${user.id}\nTên: ${user.name}\nEmail: ${user.email}`);
      } catch (error) {
        this.handleError(error, "Không tìm thấy người dùng");
      } finally {
        this.isLoading = false;
      }
    },

    // Sửa người dùng (hiện đang comment)
    async editUser(id) {
      if (!this.checkAuth()) return;

      this.isLoading = true;
      this.clearError();
      try {
        await axios.put(
          `${this.API_URL}/${id}`,
          { name: "Tên mới", email: "email mới" },
          this.getAxiosConfig()
        );
        alert("Cập nhật người dùng thành công!");
        await this.fetchUsers();
      } catch (error) {
        this.handleError(error, "Lỗi khi cập nhật người dùng");
      } finally {
        this.isLoading = false;
      }
    },

    // Xóa người dùng
    async deleteUser(id) {
      if (!this.checkAuth()) return;

      if (!confirm("Bạn có chắc muốn xóa người dùng này?")) {
        return;
      }

      this.isLoading = true;
      this.clearError();
      try {
        await axios.delete(`${this.API_URL}/${id}`, this.getAxiosConfig());
        alert("Xóa người dùng thành công!");
        await this.fetchUsers();
      } catch (error) {
        this.handleError(error, "Lỗi khi xóa người dùng");
      } finally {
        this.isLoading = false;
      }
    },

    // Đăng xuất
    logout() {
      localStorage.removeItem("token");
      alert("Đăng xuất thành công!");
      this.$router.push("/login");
    },

    // Xử lý lỗi
    handleError(error, defaultMessage) {
      if (error.response) {
        const status = error.response.status;
        const data = error.response.data;

        if (status === 401) {
          this.errorMessage = "Phiên đăng nhập hết hạn. Vui lòng đăng nhập lại.";
          this.$router.push("/login");
        } else if (status === 404) {
          this.errorMessage = "Không tìm thấy tài nguyên. Vui lòng kiểm tra lại.";
        } else if (status === 422 && data.errors) {
          const errors = Object.values(data.errors).flat().join(", ");
          this.errorMessage = `Lỗi xác thực: ${errors}`;
        } else if (status === 500) {
          this.errorMessage = "Lỗi máy chủ. Vui lòng thử lại sau.";
        } else {
          this.errorMessage = data.message || defaultMessage;
        }
      } else if (error.request) {
        this.errorMessage = "Không thể kết nối đến máy chủ. Vui lòng kiểm tra kết nối mạng.";
      } else {
        this.errorMessage = defaultMessage;
      }
      console.error(error);
    },
  },
  mounted() {
    this.fetchUsers();
  },
};
</script>

<style scoped>
.container {
  max-width: 800px;
  margin: 20px auto;
  padding: 20px;
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.logout {
  text-align: right;
  margin-bottom: 20px;
}

.logout button {
  background-color: #ff4d4f;
  padding: 8px 16px;
}

h2,
h3 {
  color: #333;
  margin-bottom: 20px;
}

.form-group {
  margin-bottom: 20px;
}

input,
button {
  margin: 5px;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 14px;
}

button {
  background-color: #6a5af9;
  color: #fff;
  border: none;
  cursor: pointer;
}

button:hover {
  background-color: #5a4af9;
}

button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

th,
td {
  color: #333;
  border: 1px solid #ddd;
  padding: 10px;
  text-align: left;
}

th {
  background-color: #f2f2f2;
  color: #333;
}

.error-message {
  color: red;
  background-color: #ffe6e6;
  padding: 10px;
  border-radius: 5px;
  margin-bottom: 20px;
}

.loading {
  color: #6a5af9;
  margin: 10px 0;
}
</style>