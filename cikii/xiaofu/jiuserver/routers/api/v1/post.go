package v1

import (
	"log"
	"net/http"
	"time"

	"github.com/gin-gonic/gin"
	"github.com/unknwon/com"
	"go.mongodb.org/mongo-driver/bson/primitive"

	"icode.cikii.com/cikii/xiaofu/jiuserver/models"
	"icode.cikii.com/cikii/xiaofu/jiuserver/pkg/lib/errorcode"
)

// GetPosts func
func GetPosts(c *gin.Context) {

	conds := make(map[string]interface{})

	if _id := c.Query("_id"); _id != "" {
		bsonID, err := primitive.ObjectIDFromHex(_id)
		if err != nil {
			log.Fatal(err)
		}
		conds["_id"] = bsonID
	}
	if name := c.Query("name"); name != "" {
		conds["name"] = name
	}

	if arg := c.Query("state"); arg != "" {
		state := com.StrTo(arg).MustInt()
		conds["state"] = state
	}

	errno := errorcode.SUCCESS
	filter, err := models.ConvertToDoc(conds)
	if err != nil {
		log.Fatal(err)
	}
	ret, err := models.FindPostByField(filter)

	c.JSON(http.StatusOK, gin.H{
		"errno": errno,
		"msg":   errorcode.GetMsg(errno),
		"data":  ret,
	})
}

// AddPost func
func AddPost(c *gin.Context) {
	var post models.Post
	var errno int

	if c.ShouldBind(&post) == nil {
		if IsPostExistByTitle(post.Title) {
			log.Printf("post title %s is already exist", post.Title)
			c.JSON(http.StatusOK, gin.H{
				"errno": errorcode.POST_TITLE_EXSIT,
				"msg":   errorcode.GetMsg(errorcode.POST_TITLE_EXSIT),
				"data":  nil,
			})
			return
		}
		ret, err := models.CreatePost(post)
		if err != nil {
			errno = errorcode.ERROR
		} else {
			errno = errorcode.SUCCESS
		}
		log.Println("Insert result is:", ret)
	} else {
		errno = errorcode.ERROR
	}

	c.JSON(http.StatusOK, gin.H{
		"errno": errno,
		"msg":   errorcode.GetMsg(errno),
		"data":  nil,
	})
}

// UpdatePost func
func UpdatePost(c *gin.Context) {
	var post models.Post
	var errno = errorcode.SUCCESS
	var ret interface{}

	if c.ShouldBind(&post) != nil {
		errno = errorcode.ERROR
	}

	if id := c.Param("id"); id != "" {
		oid, err := primitive.ObjectIDFromHex(id)
		if err != nil {
			errno = errorcode.ERROR
		}
		post.ID = oid
		now := time.Now()
		post.Utime = now.Unix()
		ret, err = models.UpdatePost(post)
		if err != nil {
			errno = errorcode.ERROR
		}
	} else {
		errno = errorcode.ERROR
	}

	c.JSON(http.StatusOK, gin.H{
		"errno": errno,
		"msg":   errorcode.GetMsg(errno),
		"data":  ret,
	})

}

// DeletePost func
func DeletePost(c *gin.Context) {
	var errno = errorcode.ERROR
	var ret interface{}
	var err error

	if id := c.Param("id"); id != "" {
		ret, err = models.DeletePostByID(id)
		if err == nil {
			errno = errorcode.SUCCESS
		}
	}

	c.JSON(http.StatusOK, gin.H{
		"errno": errno,
		"msg":   errorcode.GetMsg(errno),
		"data":  ret,
	})
}

// GetPostByID func
func GetPostByID(c *gin.Context) {

	var errno = errorcode.ERROR
	var ret interface{}

	if id := c.Param("id"); id != "" {
		ret = models.FindPostByID(id)
		if ret != nil {
			errno = errorcode.SUCCESS
		}
	}

	c.JSON(http.StatusOK, gin.H{
		"errno": errno,
		"msg":   errorcode.GetMsg(errno),
		"data":  ret,
	})

}

// IsPostExistByTitle func
func IsPostExistByTitle(title string) bool {
	ret := models.FindPostByTitle(title)
	if len(ret) == 0 {
		return false
	}
	return true
}
