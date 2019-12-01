package errorcode

const (
	// SUCCESS SUCCESS
	SUCCESS = 0
	// ERROR ERROR
	ERROR = -1
	// HTTP_SUCCESS_CODE HTTP_SUCCESS_CODE
	HTTP_SUCCESS_CODE = 200
	// HTTP_FAILED_CODE HTTP_FAILED_CODE
	HTTP_FAILED_CODE = 500
)

// ErrMsg error message
var ErrMsg = map[int]string{
	SUCCESS:           "success",
	ERROR:             "failed",
	HTTP_SUCCESS_CODE: "http 200",
	HTTP_FAILED_CODE:  "http internal error",
}

// GetMsg method
func GetMsg(code int) string {
	msg, ok := ErrMsg[code]
	if ok {
		return msg
	}
	return ErrMsg[ERROR]
}
