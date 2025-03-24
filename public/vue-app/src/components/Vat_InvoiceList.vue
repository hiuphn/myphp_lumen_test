<template>
  <div class="vat-invoice">
    <div class="container">
      <h1 class="page-title">Danh Sách Hóa Đơn VAT</h1>
      
      <!-- Filters -->
      <div class="filters-card">
        <div class="filter-group">
          <div class="input-wrapper">
            <label>Short Name</label>
            <input 
              type="text" 
              v-model="filters.short_name" 
              placeholder="Nhập tên viết tắt" 
              class="modern-input"
            />
          </div>
          <div class="input-wrapper">
            <label>Business Name</label>
            <input 
              type="text" 
              v-model="filters.business_name" 
              placeholder="Nhập tên công ty" 
              class="modern-input"
            />
          </div>
          <div class="input-wrapper">
            <label>Buyer Name</label>
            <input 
              type="text" 
              v-model="filters.buyer_name" 
              placeholder="Nhập người mua" 
              class="modern-input"
            />
          </div>
          <div class="input-wrapper">
            <label>Tax Code</label>
            <input 
              type="text" 
              v-model="filters.tax_code" 
              placeholder="Nhập mã số thuế" 
              class="modern-input"
            />
          </div>
        </div>
        <button @click="fetchVatInvoices" class="search-btn">Tìm Kiếm</button>
      </div>

      <!-- Table -->
      <div class="table-wrapper">
        <table class="modern-table">
          <thead>
            <tr>
              <th>Short Name</th>
              <th>Business Name</th>
              <th>Buyer Name</th>
              <th>Tax Code</th>
              <th>Updated At</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="invoice in vatInvoices" :key="invoice.id" class="table-row">
              <td>{{ invoice.short_name }}</td>
              <td>{{ invoice.business_name }}</td>
              <td>{{ invoice.buyer_name }}</td>
              <td>{{ invoice.tax_code }}</td>
              <td>{{ invoice.updated_at }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination-section">
        <div class="pagination-info">
          Hiển thị {{ displayedRows }} / {{ totalRecords }} dòng
        </div>
        <div class="pagination-controls">
          <label>Số dòng mỗi trang:</label>
          <select v-model="perPage" @change="fetchVatInvoices" class="modern-select">
            <option :value="1">1</option>
            <option :value="2">2</option>
            <option :value="3">3</option>
          </select>
          <span>Tổng số trang: {{ lastPage }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      vatInvoices: [],
      filters: {
        short_name: "",
        business_name: "",
        buyer_name: "",
        tax_code: "",
      },
      currentPage: "",
      lastPage: "",
      perPage: "",
      totalRecords: 0,
      displayedRows: 0,
    };
  },
  methods: {
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
    async fetchVatInvoices() {
      if (!this.checkAuth()) return;
      this.isLoading = true;
      this.clearError();
      try {
        const response = await axios.get("http://localhost:8000/api/vat-invoices", {
          params: {
            ...this.filters,
            per_page: this.perPage,
            page: this.currentPage,
          },
          ...this.getAxiosConfig(),
        });
        this.vatInvoices = response.data.data; 
        this.currentPage = response.data.currentPage; 
        this.lastPage = response.data.lastPage; 
        this.perPage = response.data.perPage; 
        this.totalRecords = response.data.totalRecords; 
        this.displayedRows = this.vatInvoices.length;
      } catch (error) {
        console.error("Lỗi khi lấy dữ liệu:", error);
      }
    },
    changePage(page) {
      this.currentPage = page;
      this.fetchVatInvoices();
    },
  },
  mounted() {
    this.fetchVatInvoices();
  },
};
</script>

<style scoped>
.vat-invoice {
  font-family: 'Inter', sans-serif;
  background: #f5f7fa;
  padding: 40px 0;
  min-height: 100vh;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.page-title {
  font-size: 28px;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 30px;
  text-align: center;
}

.filters-card {
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  margin-bottom: 30px;
}

.filter-group {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
  margin-bottom: 20px;
}

.input-wrapper {
  display: flex;
  flex-direction: column;
}

.input-wrapper label {
  font-size: 14px;
  color: #4a4a4a;
  margin-bottom: 6px;
  font-weight: 500;
}

.modern-input {
  padding: 12px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.3s ease;
}

.modern-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.search-btn {
  background: #3b82f6;
  color: white;
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.3s ease;
  display: block;
  margin: 0 auto;
}

.search-btn:hover {
  background: #2563eb;
}

.table-wrapper {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.modern-table {
  width: 100%;
  border-collapse: collapse;
}

.modern-table th {
  background: #f8fafc;
  padding: 16px;
  text-align: left;
  font-weight: 600;
  color: #4a4a4a;
  font-size: 14px;
}

.modern-table td {
  padding: 16px;
  border-bottom: 1px solid #f1f5f9;
  color: #64748b;
  font-size: 14px;
}

.table-row:hover {
  background: #f8fafc;
  transition: background 0.2s ease;
}

.pagination-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  font-size: 14px;
  color: #64748b;
}

.modern-select {
  padding: 8px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background: white;
  margin: 0 10px;
  cursor: pointer;
  color: #1a1a1a;
}

.modern-select:focus {
  outline: none;
  border-color: #3b82f6;
}
</style>