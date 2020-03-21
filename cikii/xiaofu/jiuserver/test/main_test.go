package test

import (
	"net/http"
	"net/http/httptest"
	"testing"

	"github.com/gin-gonic/gin"
	"github.com/stretchr/testify/assert"
	"icode.cikii.com/cikii/xiaofu/jiuserver/routers"
)

var router *gin.Engine

func init() {
	gin.SetMode(gin.TestMode)
	router = routers.InitRouter()
}
func TestIndexGetRouter(t *testing.T) {
	w := httptest.NewRecorder()
	req, _ := http.NewRequest(http.MethodGet, "/api/v1/index", nil)
	router.ServeHTTP(w, req)
	assert.Equal(t, http.StatusOK, w.Code)
	assert.Contains(t, w.Body.String(), "Hello")
}
