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
	// TAG_NAME_EXSIT
	TAG_NAME_EXSIT = 1000001
	// POST_TITLE_EXSIT
	POST_TITLE_EXSIT = 200001
)

// ErrMsg error message
var ErrMsg = map[int]string{
	SUCCESS:           "success",
	ERROR:             "failed",
	HTTP_SUCCESS_CODE: "http 200",
	HTTP_FAILED_CODE:  "http internal error",
	TAG_NAME_EXSIT:    "tag name already exsit",
	POST_TITLE_EXSIT:  "post title already exsit",
}

// GetMsg method
func GetMsg(code int) string {
	msg, ok := ErrMsg[code]
	if ok {
		return msg
	}
	return ErrMsg[ERROR]
}
