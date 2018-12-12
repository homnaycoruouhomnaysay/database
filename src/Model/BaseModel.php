<?php
/**
 * Project database.
 * Created by PhpStorm.
 * User: 713uk13m <dev@nguyenanhung.com>
 * Date: 10/16/18
 * Time: 11:22
 */

namespace nguyenanhung\MyDatabase\Model;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use nguyenanhung\MyDebug\Debug;
use nguyenanhung\MyDatabase\Interfaces\ProjectInterface;
use nguyenanhung\MyDatabase\Interfaces\ModelInterface;

/**
 * Class BaseModel
 *
 * Class Base Model sử dụng Query Builder của Illuminate Database
 *
 * Class này chỉ khai báo các hàm cơ bản và thông dụng trong quá trính sử dụng
 * các cú pháp, function khác đều có thể sử dụng theo tài liệu chính thức của Illuminate Database
 *
 * @see       https://laravel.com/docs/5.4/database
 * @see       https://packagist.org/packages/illuminate/database#v5.4.36
 *
 * @package   nguyenanhung\MyDatabase\Model
 * @author    713uk13m <dev@nguyenanhung.com>
 * @copyright 713uk13m <dev@nguyenanhung.com>
 * @since     2018-10-17
 * @version   0.1.2
 */
class BaseModel implements ProjectInterface, ModelInterface, BaseModelInterface
{
    /** @var object Đối tượng khởi tạo dùng gọi đến Class Debug \nguyenanhung\MyDebug\Debug */
    protected $debug;
    /** @var array|null Mảng dữ liệu chứa thông tin database cần kết nối tới */
    protected $database = NULL;
    /** @var string|null Bảng cần lấy dữ liệu */
    protected $table = NULL;
    /** @var object|null Đối tượng khởi tạo dùng gọi đến Class Capsule Manager \Illuminate\Database\Capsule\Manager */
    protected $db = NULL;
    /** @var string DB Name */
    protected $dbName = 'default';
    /** @var bool Cấu hình trạng thái Debug, TRUE nếu bật, FALSE nếu tắt */
    public $debugStatus = FALSE;
    /** @var null|string Cấu hình Level Debug */
    public $debugLevel = NULL;
    /** @var null|bool|string Cấu hình thư mục lưu trữ Log, VD: /your/to/path */
    public $debugLoggerPath = NULL;
    /** @var null|string Cấu hình File Log, VD: Log-2018-10-15.log | Log-date('Y-m-d').log */
    public $debugLoggerFilename = NULL;
    /** @var string Primary Key Default */
    public $primaryKey = 'id';

    /**
     * BaseModel constructor.
     *
     * @param array $database
     */
    public function __construct($database = [])
    {
        $this->debug = new Debug();
        if ($this->debugStatus === TRUE) {
            $this->debug->setDebugStatus($this->debugStatus);
            if ($this->debugLevel) {
                $this->debug->setGlobalLoggerLevel($this->debugLevel);
            }
            if ($this->debugLoggerPath) {
                $this->debug->setLoggerPath($this->debugLoggerPath);
            }
            if (empty($this->debugLoggerFilename)) {
                $this->debugLoggerFilename = 'Log-' . date('Y-m-d') . '.log';
            }
            $this->debug->setLoggerSubPath(__CLASS__);
            $this->debug->setLoggerFilename($this->debugLoggerFilename);
        }
        // Cấu trúc kết nối Database qua __construct
        if (!empty($database)) {
            $this->database = $database;
        }
        if (is_array($this->database) && !empty($this->database)) {
            $this->db = new DB;
            $this->db->addConnection($this->database);
            $this->db->setEventDispatcher(new Dispatcher(new Container));
            $this->db->setAsGlobal();
            $this->db->bootEloquent();
        }
    }

    /**
     * BaseModel destructor.
     */
    public function __destruct()
    {
    }

    /**
     * Hàm lấy thông tin phiên bản Package
     *
     * @author  : 713uk13m <dev@nguyenanhung.com>
     * @time    : 10/13/18 15:12
     *
     * @return mixed|string Current Project Version, VD: 0.1.0
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * Function connection
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 23:10
     *
     * @return $this
     */
    public function connection()
    {
        if (!is_object($this->db)) {
            $this->db = new DB;
            $this->db->addConnection($this->database);
            $this->db->setEventDispatcher(new Dispatcher(new Container));
            $this->db->setAsGlobal();
            $this->db->bootEloquent();
        }

        return $this;
    }

    /**
     * Function disconnect
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-02 21:55
     *
     * @return $this
     */
    public function disconnect()
    {
        $this->db = NULL;
        unset($this->db);

        return $this;
    }

    /**
     * Function getConnection
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 22:27
     *
     * @return object
     */
    public function getConnection()
    {
        return $this->db;
    }

    /**
     * Function getConnectionName
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 22:28
     *
     * @return string
     */
    public function getConnectionName()
    {
        return $this->dbName;
    }

    /**
     * Hàm set và kết nối cơ sở dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:43
     *
     * @param array  $database Mảng dữ liệu thông tin DB cần kết nối
     * @param string $name     Tên DB kết nối
     *
     * @return  $this;
     *
     * @see   https://github.com/nguyenanhung/database/tree/master/src/Repository/config/example_db.php
     * @see   https://packagist.org/packages/illuminate/database#v5.4.36
     */
    public function setDatabase($database = [], $name = 'default')
    {
        $this->database = $database;
        $this->dbName   = $name;

        return $this;
    }

    /**
     * Function getDatabase
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 23:07
     *
     * @return array|null
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Hàm set và kết nối đến bảng dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:43
     *
     * @param string $table Bảng cần lấy dữ liệu
     *
     * @return  $this;
     */
    public function setTable($table = '')
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Function getTable
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-01 23:07
     *
     * @return string|null
     */
    public function getTable()
    {
        return $this->table;
    }

    /*************************** DATABASE METHOD ***************************/
    /**
     * Function checkExistsTable
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-12 14:58
     *
     * @return bool
     */
    public function checkExistsTable()
    {
        $this->connection();
        $schema = DB::schema();

        return $schema->hasTable($this->table);
    }

    /**
     * Hàm truncate bảng dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:15
     *
     * @see   https://laravel.com/docs/5.4/queries#deletes
     *
     */
    public function truncate()
    {
        DB::table($this->table)->truncate();
    }

    /**
     * Hàm đếm toàn bộ bản ghi tồn tại trong bảng
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:43
     *
     * @return int
     */
    public function countAll()
    {
        $this->connection();
        $db = DB::table($this->table);
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        return $db->count();
    }

    /**
     * Hàm kiểm tra sự tồn tại bản ghi theo tham số đầu vào
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:45
     *
     * @param string|array $whereValue Giá trị cần kiểm tra
     * @param string|null  $whereField Field tương ứng, ví dụ: ID
     *
     * @return int Số lượng bàn ghi tồn tại phù hợp với điều kiện đưa ra
     */
    public function checkExists($whereValue = '', $whereField = 'id')
    {
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($whereValue) && count($whereValue) > 0) {
            foreach ($whereValue as $column => $column_value) {
                if (is_array($column_value)) {
                    $db->whereIn($column, $column_value);
                } else {
                    $db->where($column, self::OPERATOR_EQUAL_TO, $column_value);
                }
            }
        } else {
            $db->where($whereField, self::OPERATOR_EQUAL_TO, $whereValue);
        }
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        return $db->count();
    }

    /**
     * Hàm kiểm tra sự tồn tại bản ghi theo tham số đầu vào - Đa điều kiện
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 11:45
     *
     * @param string|array $whereValue Giá trị cần kiểm tra
     * @param string|null  $whereField Field tương ứng, ví dụ: ID
     *
     * @return int Số lượng bàn ghi tồn tại phù hợp với điều kiện đưa ra
     */
    public function checkExistsWithMultipleWhere($whereValue = '', $whereField = 'id')
    {
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($whereValue) && count($whereValue) > 0) {
            foreach ($whereValue as $key => $value) {
                if (is_array($value['value'])) {
                    $db->whereIn($value['field'], $value['value']);
                } else {
                    $db->where($value['field'], $value['operator'], $value['value']);
                }
            }
        } else {
            $db->where($whereField, self::OPERATOR_EQUAL_TO, $whereValue);
        }
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        return $db->count();
    }

    /**
     * Hàm lấy bản ghi mới nhất theo điều kiện
     *
     * Mặc định giá trị so sánh dựa trên column created_at
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 01:06
     *
     * @param array  $selectField Danh sách các column cần lấy
     * @param string $byColumn    Column cần so sánh dữ liệu, mặc định sẽ sử dụng column created_at
     *
     * @see   https://laravel.com/docs/5.4/queries#ordering-grouping-limit-and-offset
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|null|object Object dữ liệu đầu ra
     *                                                                                            của bản ghi
     */
    public function getLatest($selectField = ['*'], $byColumn = 'created_at')
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        $this->connection();
        $db = DB::table($this->table)->latest($byColumn);
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        return $db->first($selectField);
    }

    /**
     * Hàm lấy bản ghi cũ nhất nhất theo điều kiện
     *
     * Mặc định giá trị so sánh dựa trên column created_at
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/17/18 01:06
     *
     * @param array  $selectField Danh sách các column cần lấy
     * @param string $byColumn    Column cần so sánh dữ liệu, mặc định sẽ sử dụng column created_at
     *
     * @see   https://laravel.com/docs/5.4/queries#ordering-grouping-limit-and-offset
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|null|object Object dữ liệu đầu ra
     *                                                                                            của bản ghi
     */
    public function getOldest($selectField = ['*'], $byColumn = 'created_at')
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        $this->connection();
        $db = DB::table($this->table)->oldest($byColumn);
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());

        return $db->first($selectField);
    }

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
     * @param array|string      $value       Giá trị cần kiểm tra
     * @param null|string       $field       Field tương ứng, ví dụ: ID
     * @param null|string       $format      Format dữ liệu đầu ra: null, json, array, base, result
     * @param null|string|array $selectField Các field cần lấy
     *
     * @see   https://laravel.com/docs/5.4/queries#selects
     *
     * @return object|array|\Illuminate\Support\Collection|string Mảng|String|Object dữ liều phụ hợp với yêu cầu
     *                                                     map theo biến format truyền vào
     */
    public function getInfo($value = '', $field = 'id', $format = NULL, $selectField = NULL)
    {
        $this->connection();
        $format = strtolower($format);
        if (!empty($selectField)) {
            if (!is_array($selectField)) {
                $selectField = [$selectField];
            }
            $db = DB::table($this->table)->select($selectField);
        } else {
            $db = DB::table($this->table)->select();
        }
        if (is_array($value) && count($value) > 0) {
            foreach ($value as $f => $v) {
                if (is_array($v)) {
                    $db->whereIn($f, $v);
                } else {
                    $db->where($f, self::OPERATOR_EQUAL_TO, $v);
                }
            }
        } else {
            $db->where($field, self::OPERATOR_EQUAL_TO, $value);
        }
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        if ($format == 'result') {
            $result = $db->get();
            $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        } else {
            $result = $db->first();
            $this->debug->debug(__FUNCTION__, 'Format is get first Result => ' . json_encode($result));
        }
        if ($format == 'json') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Json');

            return $result->toJson();
        } elseif ($format == 'array') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Array');

            return $result->toArray();
        } elseif ($format == 'base') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Base');

            return $result->toBase();
        } else {
            if ($format == 'result') {
                if ($result->count() <= 0) {
                    return NULL;
                }
            }

            return $result;
        }
    }

    /**
     * Hàm lấy thông tin bản ghi theo tham số đầu vào - Đa điều kiện
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/26/18 16:40
     *
     * @param array|string      $wheres      Giá trị cần kiểm tra
     * @param null|string       $field       Field tương ứng, ví dụ: ID
     * @param null|string       $format      Format dữ liệu đầu ra: null, json, array, base, result
     * @param null|string|array $selectField Các field cần lấy
     *
     * @return array|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|\Illuminate\Support\Collection|null|object|string
     */
    public function getInfoWithMultipleWhere($wheres = '', $field = 'id', $format = NULL, $selectField = NULL)
    {
        $this->connection();
        $format = strtolower($format);
        if (!empty($selectField)) {
            if (!is_array($selectField)) {
                $selectField = [$selectField];
            }
            $db = DB::table($this->table)->select($selectField);
        } else {
            $db = DB::table($this->table)->select();
        }
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $key => $value) {
                if (is_array($value['value'])) {
                    $db->whereIn($value['field'], $value['value']);
                } else {
                    $db->where($value['field'], $value['operator'], $value['value']);
                }
            }
        } else {
            $db->where($field, self::OPERATOR_EQUAL_TO, $wheres);
        }
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        if ($format == 'result') {
            $result = $db->get();
            $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        } else {
            $result = $db->first();
            $this->debug->debug(__FUNCTION__, 'Format is get first Result => ' . json_encode($result));
        }
        if ($format == 'json') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Json');

            return $result->toJson();
        } elseif ($format == 'array') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Array');

            return $result->toArray();
        } elseif ($format == 'base') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Base');

            return $result->toBase();
        } else {
            if ($format == 'result') {
                if ($result->count() <= 0) {
                    return NULL;
                }
            }

            return $result;
        }
    }

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
     * @see   https://laravel.com/docs/5.4/queries#selects
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|mixed|null|object
     */
    public function getValue($value = '', $field = 'id', $fieldOutput = '')
    {
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($value) && count($value) > 0) {
            foreach ($value as $column => $column_value) {
                if (is_array($column_value)) {
                    $db->whereIn($column, $column_value);
                } else {
                    $db->where($column, self::OPERATOR_EQUAL_TO, $column_value);
                }
            }
        } else {
            $db->where($field, self::OPERATOR_EQUAL_TO, $value);
        }
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        $result = $db->first();
        $this->debug->debug(__FUNCTION__, 'Result from DB => ' . json_encode($result));
        if (!empty($fieldOutput) && isset($result->$fieldOutput)) {
            $this->debug->debug(__FUNCTION__, 'Tìm thấy thông tin cột dữ liệu ' . $fieldOutput . ' -> ' . $result->$fieldOutput);

            return $result->$fieldOutput;
        } else {
            $this->debug->error(__FUNCTION__, 'Không tìm thấy cột dữ liệu ' . $fieldOutput);

            return $result;
        }
    }

    /**
     * Hàm lấy giá trị 1 field của bản ghi dựa trên điều kiện 1 bản ghi đầu vào - Đa điều kiện
     *
     * Đây là hàm cơ bản, chỉ áp dụng check theo 1 field
     *
     * Lấy bản ghi đầu tiên phù hợp với điều kiện
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/26/18 16:41
     *
     * @param string $wheres      Giá trị cần kiểm tra
     * @param string $field       Field tương ứng với giá tri kiểm tra, ví dụ: ID
     * @param string $fieldOutput field kết quả đầu ra
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|mixed|null|object
     */
    public function getValueWithMultipleWhere($wheres = '', $field = 'id', $fieldOutput = '')
    {
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $key => $value) {
                if (is_array($value['value'])) {
                    $db->whereIn($value['field'], $value['value']);
                } else {
                    $db->where($value['field'], $value['operator'], $value['value']);
                }
            }
        } else {
            $db->where($field, self::OPERATOR_EQUAL_TO, $wheres);
        }
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        $result = $db->first();
        $this->debug->debug(__FUNCTION__, 'Result from DB => ' . json_encode($result));
        if (!empty($fieldOutput) && isset($result->$fieldOutput)) {
            $this->debug->debug(__FUNCTION__, 'Tìm thấy thông tin cột dữ liệu ' . $fieldOutput . ' -> ' . $result->$fieldOutput);

            return $result->$fieldOutput;
        } else {
            $this->debug->error(__FUNCTION__, 'Không tìm thấy cột dữ liệu ' . $fieldOutput);

            return $result;
        }
    }

    /**
     * Hàm lấy danh sách Distinct toàn bộ bản ghi trong 1 bảng
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 13:59
     *
     * @param string $selectField Mảng dữ liệu danh sách các field cần so sánh
     *
     * @see   https://laravel.com/docs/5.4/queries#selects
     *
     * @return \Illuminate\Support\Collection|object|array
     */
    public function getDistinctResult($selectField = '')
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        $this->connection();
        $db = DB::table($this->table);
        $db->distinct();
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        $result = $db->get($selectField);
        $this->debug->debug(__FUNCTION__, 'Result from DB => ' . json_encode($result));

        return $result;
    }

    /**
     * Hàm getResultDistinct là alias của hàm getDistinctResult
     *
     * Các tham số đầu ra và đầu vào theo quy chuẩn của hàm getDistinctResult
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 23:49
     *
     * @param string $selectField Mảng dữ liệu danh sách các field cần so sánh
     *
     * @return \Illuminate\Support\Collection|object|array
     */
    public function getResultDistinct($selectField = '')
    {
        return $this->getDistinctResult($selectField);
    }

    /**
     * Function getResult
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 16:14
     *
     * @param array|string $wheres              Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     * @param string|array $selectField         Mảng dữ liệu danh sách các field cần so sánh
     * @param null|string  $options             Mảng dữ liệu các cấu hình tùy chọn
     *                                          example $options = [
     *                                          'format' => null,
     *                                          'orderBy => [
     *                                          'id' => 'desc'
     *                                          ]
     *                                          ];
     *
     * @see   https://laravel.com/docs/5.4/queries#selects
     *
     * @return object|array|\Illuminate\Support\Collection|string Mảng|String|Object dữ liều phụ hợp với yêu cầu
     *                                                     map theo biến format truyền vào
     */
    public function getResult($wheres = [], $selectField = '*', $options = NULL)
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        if (isset($options['format'])) {
            $format = strtolower($options['format']);
        } else {
            $format = NULL;
        }
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $field => $value) {
                if (is_array($value)) {
                    $db->whereIn($field, $value);
                } else {
                    $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                }
            }
        } else {
            $db->where($this->primaryKey, self::OPERATOR_EQUAL_TO, $wheres);
        }
        if (isset($options['orderBy']) && is_array($options['orderBy'])) {
            foreach ($options['orderBy'] as $column => $direction) {
                $db->orderBy($column, $direction);
            }
        }
        if (isset($options['orderBy']) && $options['orderBy'] == 'random') {
            $db->inRandomOrder();
        }
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        $result = $db->get($selectField);
        $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        if ($format == 'json') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Json');

            return $result->toJson();
        } elseif ($format == 'array') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Array');

            return $result->toArray();
        } elseif ($format == 'base') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Base');

            return $result->toBase();
        } else {
            return $result;
        }
    }

    /**
     * Function getResult - Đa điều kiện
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 16:14
     *
     * @param array|string $wheres              Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     * @param string|array $selectField         Mảng dữ liệu danh sách các field cần so sánh
     * @param null|string  $options             Mảng dữ liệu các cấu hình tùy chọn
     *                                          example $options = [
     *                                          'format' => null,
     *                                          'orderBy => [
     *                                          'id' => 'desc'
     *                                          ]
     *                                          ];
     *
     * @see   https://laravel.com/docs/5.4/queries#selects
     *
     * @return object|array|\Illuminate\Support\Collection|string Mảng|String|Object dữ liều phụ hợp với yêu cầu
     *                                                     map theo biến format truyền vào
     */
    public function getResultWithMultipleWhere($wheres = [], $selectField = '*', $options = NULL)
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        if (isset($options['format'])) {
            $format = strtolower($options['format']);
        } else {
            $format = NULL;
        }
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $field => $value) {
                if (is_array($value['value'])) {
                    $db->whereIn($value['field'], $value['value']);
                } else {
                    $db->where($value['field'], $value['operator'], $value['value']);
                }
            }
        }
        if (isset($options['orderBy']) && is_array($options['orderBy'])) {
            foreach ($options['orderBy'] as $column => $direction) {
                $db->orderBy($column, $direction);
            }
        }
        if (isset($options['orderBy']) && $options['orderBy'] == 'random') {
            $db->inRandomOrder();
        }
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        $result = $db->get($selectField);
        $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        if ($format == 'json') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Json');

            return $result->toJson();
        } elseif ($format == 'array') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Array');

            return $result->toArray();
        } elseif ($format == 'base') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Base');

            return $result->toBase();
        } else {
            return $result;
        }
    }

    /**
     * Function countResult
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 11/25/18 14:10
     *
     * @param array  $wheres
     * @param string $selectField
     *
     * @return int
     */
    public function countResult($wheres = [], $selectField = '*')
    {
        if (!is_array($selectField)) {
            $selectField = [$selectField];
        }
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $field => $value) {
                if (is_array($value)) {
                    $db->whereIn($field, $value);
                } else {
                    $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                }
            }
        } else {
            $db->where($this->primaryKey, self::OPERATOR_EQUAL_TO, $wheres);
        }
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        $result = $db->get($selectField);
        $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        $totalItem = $result->count();
        $this->debug->debug(__FUNCTION__, 'Total Item Result => ' . json_encode($totalItem));

        return $totalItem;
    }

    /**
     * Function getResultWithSimpleJoin
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 02:03
     *
     * @param array  $joins
     * @param string $select
     * @param null   $options
     *
     * @return object|array|\Illuminate\Support\Collection|string
     */
    public function getResultWithSimpleJoin($joins = [], $select = '*', $options = NULL)
    {
        if (isset($options['format'])) {
            $format = strtolower($options['format']);
        } else {
            $format = NULL;
        }
        $db = DB::table($this->table);
        foreach ($joins as $key => $join) {
            $db->join($join['table'], $join['first'], $join['operator'], $join['second']);
        }
        $result = $db->select($select)->get();
        $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        if ($format == 'json') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Json');

            return $result->toJson();
        } elseif ($format == 'array') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Array');

            return $result->toArray();
        } elseif ($format == 'base') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Base');

            return $result->toBase();
        } else {
            return $result;
        }
    }

    /**
     * Function getResultWithSimpleLeftJoin
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 02:05
     *
     * @param array  $joins
     * @param string $select
     * @param null   $options
     *
     * @return object|array|\Illuminate\Support\Collection|string
     */
    public function getResultWithSimpleLeftJoin($joins = [], $select = '*', $options = NULL)
    {
        if (isset($options['format'])) {
            $format = strtolower($options['format']);
        } else {
            $format = NULL;
        }
        $db = DB::table($this->table);
        foreach ($joins as $key => $join) {
            $db->leftJoin($join['table'], $join['first'], $join['operator'], $join['second']);
        }
        $result = $db->select($select)->get();
        $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        if ($format == 'json') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Json');

            return $result->toJson();
        } elseif ($format == 'array') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Array');

            return $result->toArray();
        } elseif ($format == 'base') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Base');

            return $result->toBase();
        } else {
            return $result;
        }
    }

    /**
     * Function getResultWithSimpleCrossJoin
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 2018-12-03 02:06
     *
     * @param array  $joins
     * @param string $select
     * @param null   $options
     *
     * @return object|array|\Illuminate\Support\Collection|string
     */
    public function getResultWithSimpleCrossJoin($joins = [], $select = '*', $options = NULL)
    {
        if (isset($options['format'])) {
            $format = strtolower($options['format']);
        } else {
            $format = NULL;
        }
        $db = DB::table($this->table);
        foreach ($joins as $key => $join) {
            $db->crossJoin($join['table'], $join['first'], $join['operator'], $join['second']);
        }
        $result = $db->select($select)->get();
        $this->debug->debug(__FUNCTION__, 'Format is get all Result => ' . json_encode($result));
        if ($format == 'json') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Json');

            return $result->toJson();
        } elseif ($format == 'array') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Array');

            return $result->toArray();
        } elseif ($format == 'base') {
            $this->debug->debug(__FUNCTION__, 'Output Result is Base');

            return $result->toBase();
        } else {
            return $result;
        }
    }

    /**
     * Hàm thêm mới bản ghi vào bảng
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:04
     *
     * @param array $data Mảng chứa dữ liệu cần insert
     *
     * @see   https://laravel.com/docs/5.4/queries#inserts
     *
     * @return int Insert ID của bản ghi
     */
    public function add($data = [])
    {
        $this->connection();
        $db = DB::table($this->table);
        $id = $db->insertGetId($data);
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        $this->debug->info(__FUNCTION__, 'Result Insert ID: ' . $id);

        return $id;
    }

    /**
     * Hàm update dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:10
     *
     * @param array        $data   Mảng dữ liệu cần Update
     * @param array|string $wheres Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     *
     * @see   https://laravel.com/docs/5.4/queries#updates
     *
     * @return int Số bản ghi được update thỏa mãn với điều kiện đầu vào
     */
    public function update($data = [], $wheres = [])
    {
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $field => $value) {
                if (is_array($value)) {
                    $db->whereIn($field, $value);
                } else {
                    $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                }
            }
        } else {
            $db->where($this->primaryKey, self::OPERATOR_EQUAL_TO, $wheres);
        }
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        $resultId = $db->update($data);
        $this->debug->info(__FUNCTION__, 'Result Update Rows: ' . $resultId);

        return $resultId;
    }

    /**
     * Hàm xóa dữ liệu
     *
     * @author: 713uk13m <dev@nguyenanhung.com>
     * @time  : 10/16/18 14:13
     *
     * @param array|string $wheres Mảng dữ liệu hoặc giá trị primaryKey cần so sánh điều kiện để update
     *
     * @see   https://laravel.com/docs/5.4/queries#deletes
     *
     * @return int Số bản ghi đã xóa
     */
    public function delete($wheres = [])
    {
        $this->connection();
        $db = DB::table($this->table);
        if (is_array($wheres) && count($wheres) > 0) {
            foreach ($wheres as $field => $value) {
                if (is_array($value)) {
                    $db->whereIn($field, $value);
                } else {
                    $db->where($field, self::OPERATOR_EQUAL_TO, $value);
                }
            }
        } else {
            $db->where($this->primaryKey, self::OPERATOR_EQUAL_TO, $wheres);
        }
        $this->debug->info(__FUNCTION__, 'SQL Queries: ' . $db->toSql());
        $resultId = $db->delete();
        $this->debug->info(__FUNCTION__, 'Result Delete Rows: ' . $resultId);

        return $resultId;
    }
}
