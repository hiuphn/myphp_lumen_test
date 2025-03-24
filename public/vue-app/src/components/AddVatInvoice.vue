<!-- src/components/AddVatInvoice.vue -->
<template>
    <div class="container">
      <h2>Thêm Hóa Đơn VAT</h2>
      <form @submit.prevent="handleSubmit">
        <div class="form-group">
          <label for="short_name">Tên Viết Tắt <span class="required">*</span></label>
          <input
            type="text"
            id="short_name"
            v-model="form.short_name"
            placeholder="Nhập Tên Viết Tắt"
            required
            maxlength="30"
          />
          <span class="error" v-if="errors.short_name">{{ errors.short_name }}</span>
        </div>
  
        <div class="form-group">
          <label for="business_name">Tên Đăng Ký Kinh Doanh <span class="required">*</span></label>
          <input
            type="text"
            id="business_name"
            v-model="form.business_name"
            placeholder="Nhập Tên Đăng Ký Kinh Doanh"
            required
            maxlength="70"
          />
          <span class="error" v-if="errors.business_name">{{ errors.business_name }}</span>
        </div>
  
        <div class="form-group">
          <label for="buyer_name">Tên Người Mua Hàng <span class="required">*</span></label>
          <input
            type="text"
            id="buyer_name"
            v-model="form.buyer_name"
            placeholder="Nhập Tên Người Mua Hàng"
            required
            maxlength="50"
          />
          <span class="error" v-if="errors.buyer_name">{{ errors.buyer_name }}</span>
        </div>
  
        <div class="form-group">
          <label for="tax_code">Mã Số Thuế <span class="required">*</span></label>
          <input
            type="text"
            id="tax_code"
            v-model="form.tax_code"
            placeholder="Nhập Mã Số Thuế (chỉ số và '-') "
            required
            maxlength="20"
            pattern="^[0-9\-]+$"
          />
          <span class="error" v-if="errors.tax_code">{{ errors.tax_code }}</span>
        </div>
  
        <div class="form-group">
          <label for="invoice_address">Địa Chỉ Xuất Hóa Đơn <span class="required">*</span></label>
          <input
            type="text"
            id="invoice_address"
            v-model="form.invoice_address"
            placeholder="Nhập Địa Chỉ Xuất Hóa Đơn"
            required
            maxlength="255"
          />
          <span class="error" v-if="errors.invoice_address">{{ errors.invoice_address }}</span>
        </div>
  
        <div class="form-group">
          <label for="invoice_email">Email Nhận Hóa Đơn <span class="required">*</span></label>
          <input
            type="email"
            id="invoice_email"
            v-model="form.invoice_email"
            placeholder="Nhập Email Nhận Hóa Đơn"
            required
            maxlength="255"
            pattern="^[\w\.]+@[\w]+\.[\w]+(\.[\w]+)?$"
          />
          <span class="error" v-if="errors.invoice_email">{{ errors.invoice_email }}</span>
        </div>
  
        <div class="form-group">
          <label for="receiver_name">Người Nhận Hóa Đơn <span class="required">*</span></label>
          <input
            type="text"
            id="receiver_name"
            v-model="form.receiver_name"
            placeholder="Nhập Người Nhận Hóa Đơn"
            required
            maxlength="20"
          />
          <span class="error" v-if="errors.receiver_name">{{ errors.receiver_name }}</span>
        </div>
  
        <button type="submit" class="submit-btn" :disabled="isLoading">
          {{ isLoading ? "Đang Gửi..." : "Gửi Yêu Cầu" }}
        </button>
      </form>
    </div>
  </template>
  
  <script>
  export default {
    name: "AddVatInvoice",
    data() {
      return {
        form: {
          short_name: "",
          business_name: "",
          buyer_name: "",
          tax_code: "",
          invoice_address: "",
          invoice_email: "",
          receiver_name: "",
        },
        errors: {},
        errorMessage: "", // Thêm errorMessage để hiển thị lỗi chung
        isLoading: false, // Thêm isLoading để quản lý trạng thái gửi
      };
    },
    methods: {
      // Kiểm tra token và chuyển hướng nếu chưa đăng nhập
      checkAuth() {
        const token = localStorage.getItem("token");
        if (!token) {
          alert("Bạn cần đăng nhập để thực hiện hành động này.");
          this.$router.push("/login");
          return false;
        }
        return true;
      },
  
      // Thêm token vào header của fetch
      getFetchConfig() {
        const token = localStorage.getItem("token");
        return {
          method: "POST", // Chỉ định phương thức POST
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
          body: JSON.stringify(this.form), // Gửi dữ liệu form trong body
        };
      },
  
      // Xóa thông báo lỗi
      clearError() {
        this.errorMessage = "";
        this.errors = {};
      },
  
      // Xử lý gửi form
      async handleSubmit() {
        if (!this.checkAuth()) return;
  
        this.isLoading = true;
        this.clearError();
  
        try {
          const response = await fetch(
            "http://localhost:8000/api/vat-invoices", 
            this.getFetchConfig()
          );
          const data = await response.json();
  
          if (response.ok) {
            alert(data.message); 
            // Reset form
            this.form = {
              short_name: "",
              business_name: "",
              buyer_name: "",
              tax_code: "",
              invoice_address: "",
              invoice_email: "",
              receiver_name: "",
            };
          } else if (response.status === 422) {
            // Xử lý lỗi xác thực
            this.errors = data.errors;
          } else if (response.status === 401) {
            alert(data.message); // "Không xác định được khách hàng"
            this.$router.push("/login");
          } else {
            this.errorMessage = "Có lỗi xảy ra. Vui lòng thử lại.";
          }
        } catch (error) {
          console.error("Error:", error);
          this.errorMessage = "Đã xảy ra lỗi khi gửi yêu cầu.";
        } finally {
          this.isLoading = false;
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .container {
    max-width: 600px;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  }
  
  h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
  }
  
  .form-group {
    margin-bottom: 20px;
  }
  
  .form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #555;
  }
  
  .form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    color: #ffffff; /* Sửa màu chữ để có thể nhìn thấy trên nền trắng */
  }
  
  .form-group input:focus {
    outline: none;
    border-color: #6a5af9;
  }
  
  .required {
    color: red;
  }
  
  .error {
    color: red;
    font-size: 12px;
    margin-top: 5px;
    display: block;
  }
  
  .submit-btn {
    width: 100%;
    padding: 12px;
    background-color: #6a5af9;
    border: none;
    border-radius: 5px;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
  }
  
  .submit-btn:hover {
    background-color: #5a4af9;
  }
  
  .submit-btn:disabled {
    background-color: #ccc;
    cursor: not-allowed;
  }
  </style>