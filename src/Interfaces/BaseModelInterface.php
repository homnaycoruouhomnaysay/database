<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 10/16/18
 * Time: 15:42
 */

namespace nguyenanhung\MyDatabase\Interfaces;

/**
 * Interface BaseModelInterface
 *
 * @package   nguyenanhung\MyDatabase\Interfaces
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 */
interface BaseModelInterface
{
    /**
     * Hàm khởi tạo kết nối đến Cơ sở dữ liệu
     *
     * Sử dụng đối tượng DB được truyền từ bên ngoài vào
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 15:47
     *
     */
    public function connection();

    /**
     * Hàm set và kết nối cơ sở dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:43
     *
     * @param array $db Mảng dữ liệu thông tin DB cần kết nối
     */
    public function setDatabase($db = []);

    /**
     * Hàm set và kết nối đến bảng dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:43
     *
     * @param string $table Bảng cần lấy dữ liệu
     */
    public function setTable($table = '');

    /**
     * Hàm truncate bảng dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:15
     *
     */
    public function truncate();

    /**
     * Hàm đếm toàn bộ bản ghi tồn tại trong bảng
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:43
     *
     * @return int
     */
    public function countAll();

    /**
     * Hàm kiểm tra sự tồn tại bản ghi theo tham số đầu vào
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:45
     *
     * @param string $value Giá trị cần kiểm tra
     * @param string $field Field tương ứng, ví dụ: ID
     *
     * @return int Số lượng bàn ghi tồn tại phù hợp với điều kiện đưa ra
     */
    public function checkExists($value = '', $field = 'id');

    /**
     * Hàm lấy thông tin bản ghi theo tham số đầu vào
     *
     * Đây là hàm cơ bản, chỉ áp dụng check theo 1 field
     *
     * Lấy bản ghi đầu tiên phù hợp với điều kiện
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:51
     *
     * @param string      $value  Giá trị cần kiểm tra
     * @param string      $field  Field tương ứng, ví dụ: ID
     * @param null|string $format Format dữ liệu đầu ra: null, json, array, base, result
     *
     * @return array|\Illuminate\Support\Collection|string Mảng|String|Object dữ liều phụ hợp với yêu cầu
     *                                                     map theo biến format truyền vào
     */
    public function getInfo($value = '', $field = 'id', $format = NULL);

    /**
     * Hàm lấy giá trị 1 field của bản ghi dựa trên điều kiện 1 bản ghi đầu vào
     *
     * Đây là hàm cơ bản, chỉ áp dụng check theo 1 field
     *
     * Lấy bản ghi đầu tiên phù hợp với điều kiện
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:51
     *
     * @param string $value       Giá trị cần kiểm tra
     * @param string $field       Field tương ứng với giá tri kiểm tra, ví dụ: ID
     * @param string $fieldOutput field kết quả đầu ra
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|mixed|null|object
     */
    public function getValue($value = '', $field = 'id', $fieldOutput = '');

    /**
     * Hàm lấy danh sách Distinct toàn bộ bản ghi trong 1 bảng
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 13:59
     *
     * @param string $selectField Mảng dữ liệu danh sách các field cần so sánh
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDistinctResult($selectField = '');

    /**
     * Function getResult
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 16:14
     *
     * @param array|string $wheres              Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     * @param string       $selectField         Mảng dữ liệu danh sách các field cần so sánh
     * @param null|string  $options             Mảng dữ liệu các cấu hình tùy chọn
     *                                          example $options = [
     *                                          'format' => null,
     *                                          'orderBy => [
     *                                          'id' => 'desc'
     *                                          ]
     *                                          ];
     *
     * @return array|\Illuminate\Support\Collection|string Mảng|String|Object dữ liều phụ hợp với yêu cầu
     *                                                     map theo biến format truyền vào
     */
    public function getResult($wheres = [], $selectField = '*', $options = NULL);

    /**
     * Hàm thêm mới bản ghi vào bảng
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:04
     *
     * @param array $data Mảng chứa dữ liệu cần insert
     *
     * @return int Insert ID của bản ghi
     */
    public function add($data = []);

    /**
     * Hàm update dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:10
     *
     * @param array        $data   Mảng dữ liệu cần Update
     * @param array|string $wheres Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     *
     * @return int Số bản ghi được update thỏa mãn với điều kiện đầu vào
     */
    public function update($data = [], $wheres = []);

    /**
     * Hàm xóa dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:13
     *
     * @param array|string $wheres Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     *
     * @return int Số bản ghi đã xóa
     */
    public function delete($wheres = []);
}