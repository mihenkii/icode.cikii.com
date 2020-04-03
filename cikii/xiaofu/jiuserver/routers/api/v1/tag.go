package v1

import (
	"log"
	"net/http"

	"github.com/gin-gonic/gin"
	"github.com/unknwon/com"
	"go.mongodb.org/mongo-driver/bson/primitive"

	"icode.cikii.com/cikii/xiaofu/jiuserver/models"
	"icode.cikii.com/cikii/xiaofu/jiuserver/pkg/lib/errorcode"
)

// GetTags func
func GetTags(c *gin.Context) {

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
	ret, err := models.FindTagByField(filter)

	c.JSON(http.StatusOK, gin.H{
		"errno": errno,
		"msg":   errorcode.GetMsg(errno),
		"data":  ret,
	})
}

// AddTag func
func AddTag(c *gin.Context) {
	var tag models.Tag
	var errno int

	if c.ShouldBind(&tag) == nil {
		if IsTagExistByName(tag.Name) {
			log.Printf("tag %s is already exist", tag.Name)
			c.JSON(http.StatusOK, gin.H{
				"errno": errorcode.TAG_NAME_EXSIT,
				"msg":   errorcode.GetMsg(errorcode.TAG_NAME_EXSIT),
				"data":  nil,
			})
			return
		}
		ret, err := models.CreateTag(tag)
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

// UpdateTag func
func UpdateTag(c *gin.Context) {
	var tag models.Tag
	var errno = errorcode.SUCCESS
	var ret interface{}

	if c.ShouldBind(&tag) != nil {
		errno = errorcode.ERROR
	}

	if id := c.Param("id"); id != "" {
		oid, err := primitive.ObjectIDFromHex(id)
		if err != nil {
			errno = errorcode.ERROR
		}
		tag.ID = oid
		ret, err = models.UpdateTag(tag)
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

// DeleteTag func
func DeleteTag(c *gin.Context) {
	var errno = errorcode.ERROR
	var ret interface{}
	var err error

	if id := c.Param("id"); id != "" {
		ret, err = models.DeleteTagByID(id)
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

// GetTagByID func
func GetTagByID(c *gin.Context) {

	var errno = errorcode.ERROR
	var ret interface{}

	if id := c.Param("id"); id != "" {
		ret = models.GetTagByID(id)
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

// IsTagExistByName func
func IsTagExistByName(name string) bool {
	ret := models.FindTagByName(name)
	if len(ret) == 0 {
		return false
	}
	return true
}

//Index func
func Index(c *gin.Context) {
	c.HTML(200, "index.gohtml", gin.H{"title": "Hello gin"})
}
