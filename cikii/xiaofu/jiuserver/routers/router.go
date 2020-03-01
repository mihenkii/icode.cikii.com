package routers

import (
	"github.com/gin-gonic/gin"
	v1 "icode.cikii.com/cikii/xiaofu/jiuserver/routers/api/v1"
)

// InitRouter func
func InitRouter() *gin.Engine {
	r := gin.New()
	r.Use(gin.Logger())
	r.Use(gin.Recovery())

	gin.SetMode("debug")

	apiv1 := r.Group("/api/v1")
	{
		apiv1.POST("/tags", v1.AddTag)
		apiv1.GET("/tags", v1.GetTags)
		apiv1.GET("/tags/:id", v1.GetTagByID)
		apiv1.PUT("/tags/:id", v1.UpdateTag)
		apiv1.DELETE("/tags/:id", v1.DeleteTag)

		apiv1.POST("/posts", v1.AddPost)
		apiv1.GET("/posts", v1.GetPosts)
		apiv1.GET("/posts/:id", v1.GetPostByID)
		apiv1.PUT("/posts/:id", v1.UpdatePost)
		apiv1.DELETE("/posts/:id", v1.DeletePost)
	}

	r.GET("/ping", func(c *gin.Context) {
		c.JSON(200, gin.H{
			"message": "pong",
		})
	})

	return r
}
