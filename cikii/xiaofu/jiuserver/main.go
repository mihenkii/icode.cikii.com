package main

import (
	"log"
	"reflect"

	"github.com/gin-gonic/gin"
	"icode.cikii.com/cikii/xiaofu/jiuserver/models"
)

func main() {

	// post := models.Post{primitive.NewObjectID(), "这是一个title3", "这是content，有长度", 1, time.Now().Unix(), time.Now().Unix(), 1, "http://www.baidu.com", ""}
	/*
		var post models.Post
		post.ID = primitive.NewObjectID()
		post.Title = "这是一个title4"
		post.Content = "这是content，有长度"
		post.Type = 1
		post.Ctime = time.Now().Unix()
		post.Utime = time.Now().Unix()
		post.UserID = 1
		post.ReferURL = "http://www.baidu.com"
		post.Extra = ""
		models.CreatePost(post)

		result := models.FindPostByID("5deb9a1eb19bc5a841718027")
		fmt.Printf("Found a single document: %+v\n", result)

		var postForUpdate models.Post
		oid, err := primitive.ObjectIDFromHex("5deb9a1eb19bc5a841718027")
		if err != nil {
			log.Fatal(err)
		}
		postForUpdate.ID = oid
		postForUpdate.Title = "new title M2"
		models.UpdatePost(postForUpdate)

		result = models.FindPostByID("5deb9a1eb19bc5a841718027")
		fmt.Printf("Found a single document: %+v\n", result)

		var postToDelete models.Post
		postToDelete.Ctime = 1575188641
		ret := models.DeletePost(postToDelete)
		if ret.DeletedCount == 1 {
			log.Printf("delete ret: %v", ret)
		}
	*/
	allPost, err := models.FindAllPost()
	if err != nil {
		log.Fatal(err)
	}
	for index, item := range allPost {
		log.Printf("allPost %d ret: %+v\n", index, item)
		log.Printf("print %+v\n", reflect.ValueOf(item))
	}

	// fileter :=
	router := gin.Default()
	router.GET("/ping", func(c *gin.Context) {
		c.JSON(200, gin.H{
			"message": "pong",
		})
	})
	router.Run()
}
