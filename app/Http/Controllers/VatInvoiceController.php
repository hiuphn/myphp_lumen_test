<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VatInvoice;
use App\Models\VatInvoiceUpdate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
class VatInvoiceController extends Controller {
    
    public function index(Request $request) {
        $query = VatInvoice::query();

        
        // if ($request->has('short_name')) {
        //     $query->where('short_name', 'LIKE', "%{$request->short_name}%");
        // }
        // if ($request->has('business_name')) {
        //     $query->where('business_name', 'LIKE', "%{$request->business_name}%");
        // }
        // if ($request->has('buyer_name')) {
        //     $query->where('buyer_name', 'LIKE', "%{$request->buyer_name}%");
        // }
        // if ($request->has('tax_code')) {
        //     $query->where('tax_code', 'like', '%' . $request->tax_code . '%');
        // }
        $searchableFields = ['short_name', 'business_name', 'buyer_name', 'tax_code'];
        foreach ($searchableFields as $field) {
            if ($request->has($field)) {
                $query->where($field, 'LIKE', '%' . $request->$field . '%');
            }
        }
        $sortableColumns = ['short_name', 'business_name', 'buyer_name', 'tax_code', 'updated_at', 'created_at', 'invoice_email', 'receiver_name']; 
        $sortBy = $request->input('sort_by', 'updated_at'); 
        $sortOrder = $request->input('sort_order', 'desc'); 

        if (in_array($sortBy, $sortableColumns)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $totalRecords = $query->count();

        $perPage = $request->input('per_page', 3);
        $vatInvoices = $query->paginate($perPage);

        return response()->json([
            'data'          => $vatInvoices->items(), 
            'totalRecords'  => $totalRecords, 
            'displayedRows' => count($vatInvoices->items()), 
            'currentPage'   => $vatInvoices->currentPage(),
            'perPage'       => $vatInvoices->perPage(),
            'lastPage'      => $vatInvoices->lastPage(),
            'sortBy'        => $sortBy,
            'sortOrder'     => $sortOrder
        ]);

        // $vatInvoices = $query->paginate($request->input('per_page', 20));

        // return response()->json($vatInvoices);
    }

    
    public function show($id) {
        $vatInvoice = VatInvoice::find($id);
        if (!$vatInvoice) {
            return response()->json(['message' => 'Không tìm thấy hóa đơn VAT'], 404);
        }
        return response()->json($vatInvoice);
    }

    
    public function add(Request $request) {
        $customer_id = Auth::payload()->get('customer_id');
        if (!$customer_id) {
            return response()->json(['message' => 'Không xác định được khách hàng'], 401);
        }
    
        $validator = Validator::make($request->all(), [
            'short_name'     => [
                'required', 'string', 'max:30',
                Rule::unique('vat_invoices')->where(function ($query) use ($request) {
                    return $query->where('customer_id', $request->customer_id);
                })
            ],
            'business_name'  => 'required|string|max:70',
            'buyer_name'     => 'required|string|max:50',
            'tax_code'       => [
                'required', 'string', 'max:20', 'regex:/^[0-9\-]+$/',
                Rule::unique('vat_invoices')->where(function ($query) use ($request) {
                    return $query->where('customer_id', $request->customer_id);
                })
            ],
            'invoice_address'=> 'required|string|max:255',
            'invoice_email' => [
                'required',
                'regex:/^[\w\.]+@[\w]+\.[\w]+(\.[\w]+)?$/', 
                'max:255',
            ],
            'receiver_name'  => 'required|string|max:20',
        ], [
            'customer_id.required'       => 'Vui lòng chọn khách hàng.',
            'customer_id.exists'         => 'Khách hàng không tồn tại.',
            'short_name.required'        => 'Vui lòng nhập Tên Viết Tắt.',
            'short_name.unique'          => 'Tên Viết Tắt đã tồn tại.',
            'short_name.max'             => 'Tên Viết Tắt không được quá 30 ký tự.',
            'business_name.required'     => 'Vui lòng nhập Tên Đăng Ký Kinh Doanh.',
            'business_name.max'          => 'Tên Đăng Ký Kinh Doanh không được quá 70 ký tự.',
            'buyer_name.required'        => 'Vui lòng nhập Tên Người Mua Hàng.',
            'buyer_name.max'             => 'Tên Người Mua Hàng không được quá 50 ký tự.',
            'tax_code.required'          => 'Vui lòng nhập Mã Số Thuế.',
            'tax_code.unique'            => 'Mã Số Thuế đã tồn tại.',
            'tax_code.regex'             => 'Mã Số Thuế chỉ được chứa số và ký tự "-".',
            'tax_code.max'               => 'Mã Số Thuế không được quá 20 ký tự.',
            'invoice_address.required'   => 'Vui lòng nhập Địa Chỉ Xuất Hóa Đơn.',
            'invoice_address.max'        => 'Địa Chỉ Xuất Hóa Đơn không được quá 255 ký tự.',
            'invoice_email.required'     => 'Vui lòng nhập Email Nhận Hóa Đơn.',
            'invoice_email.regex'        => 'Định dạng email không hợp lệ.',
            'invoice_email.email'        => 'Định dạng email không hợp lệ.',
            'invoice_email.max'          => 'Email Nhận Hóa Đơn không được quá 255 ký tự.',
            'receiver_name.required'     => 'Vui lòng nhập Người Nhận Hóa Đơn.',
            'receiver_name.max'          => 'Người Nhận Hóa Đơn không được quá 20 ký tự.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['short_name'] = strtoupper($data['short_name']);
        $data['customer_id'] = $customer_id;

        $vatInvoiceUpdate = VatInvoiceUpdate::create([
            'updated_data'   => json_encode($data),
            'status'         => 'pending',
            'description'    => 'add'
        ]);
        return response()->json(['message' => 'Đã gửi yêu cầu thêm hóa đơn VAT', 'data' => $vatInvoiceUpdate], 201);
    }

   
    public function update(Request $request, $id) {
        $vatInvoice = VatInvoice::find($id);
        if (!$vatInvoice) {
            return response()->json(['message' => 'Không tìm thấy hóa đơn VAT'], 404);
        }
    
        $validator = Validator::make($request->all(), [
            'business_name'   => 'sometimes|string|max:70',
            'buyer_name'      => 'sometimes|string|max:50',
            'tax_code'        => [
                'sometimes', 'string', 'max:20',
                Rule::unique('vat_invoices')->ignore($id)->where(function ($query) use ($vatInvoice) {
                    return $query->where('customer_id', $vatInvoice->customer_id);
                })
            ],
            'invoice_address' => 'sometimes|string|max:255',
            'invoice_email'   => 'sometimes|email|max:255',
            'receiver_name'   => 'sometimes|string|max:20',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $vatInvoiceUpdate = VatInvoiceUpdate::create([
            'vat_invoice_id' => $id,
            'updated_data'   => json_encode($request->all()),
            'status'         => 'pending',
            'description'    => 'update'
        ]);

        Log::info("Gửi yêu cầu cập nhật hóa đơn VAT", [
            'invoice_id' => $id,
            'customer_id' => $vatInvoice->customer_id,
            'requested_data' => $request->all(),
        ]);
    
        
        return response()->json([
            'message' => 'Đã gửi yêu cầu cập nhật thông tin hóa đơn VAT thành công. Vui lòng đợi duyệt!',
            'status' => 'pending',
            'data' => $vatInvoiceUpdate
        ]);
    }
    public function approveOrReject($updateId, Request $request) {
        $updateRequest = VatInvoiceUpdate::find($updateId);
    
        if (!$updateRequest) {
            return response()->json(['message' => 'Không tìm thấy yêu cầu cập nhật'], 404);
        }
    
        if ($updateRequest->status !== 'pending') {
            return response()->json(['message' => 'Yêu cầu đã được xử lý trước đó'], 400);
        }
    
        $action = $request->input('action');
    
        if ($action === 'approve') {
            if ($updateRequest->description == 'update') {
                $vatInvoice = VatInvoice::find($updateRequest->vat_invoice_id);
                if (!$vatInvoice) {
                    return response()->json(['message' => 'Không tìm thấy hóa đơn VAT'], 404);
                }
    
                $vatInvoice->update(json_decode($updateRequest->updated_data, true));
                $vatInvoice->status = 'approved'; 
                $vatInvoice->save();
    
            } elseif ($updateRequest->description == 'delete') {
                $vatInvoice = VatInvoice::find($updateRequest->vat_invoice_id);
                if ($vatInvoice) {
                    $vatInvoice->status = 'approved'; 
                    $vatInvoice->save();
                    $vatInvoice->delete();
                }
            } elseif ($updateRequest->description == 'add') {
                $newVatInvoice = VatInvoice::create(json_decode($updateRequest->updated_data, true));
                $newVatInvoice->status = 'approved'; 
                $newVatInvoice->save();
            }
    
            $updateRequest->status = 'approved';
            $updateRequest->save();
    
            return response()->json([
                'message' => 'Yêu cầu đã được phê duyệt!',
                'description' => $updateRequest->description,
                'data' => $updateRequest
            ]);
    
        } elseif ($action === 'reject') {
            $updateRequest->status = 'rejected';
            $updateRequest->save();
    
            return response()->json([
                'message' => 'Yêu cầu đã bị từ chối!',
                'description' => $updateRequest->description,
                'data' => $updateRequest
            ]);
        }
    
        return response()->json(['message' => 'Hành động không hợp lệ!'], 400);
    }
    
    
    
    
    public function destroy($id) {
        $vatInvoice = VatInvoice::find($id);
        if (!$vatInvoice) {
            return response()->json(['message' => 'Không tìm thấy hóa đơn VAT'], 404);
        }
        if (in_array($vatInvoice->status, ['pending', 'approved'])) {
            return response()->json([
                'message' => 'Thất bại. Yêu cầu cập nhật thông tin của Quý khách đang được xử lý!',
            ], 400);
        }
        $vatInvoice->delete();
        return response()->json(['message' => 'Xóa thành công']);
    }
}
