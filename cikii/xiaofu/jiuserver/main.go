package main

import (
	"fmt"
	"time"

	"github.com/gin-gonic/gin"
	"go.mongodb.org/mongo-driver/bson/primitive"
	"icode.cikii.com/cikii/xiaofu/jiuserver/models"
)

func main() {

	// post := models.Post{primitive.NewObjectID(), "这是一个title3", "这是content，有长度", 1, time.Now().Unix(), time.Now().Unix(), 1, "http://www.baidu.com", ""}
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
	post.InsertPost(post)
	// post.ID = primitive.NewObjectID()
	// post2 := Post{primitive.NewObjectID(), "这是一个title", "这是content，有长度", 1, time.Now().Unix(), time.Now().Unix(), 1, "http://www.baidu.com", ""}
	// collection := client.Database("cikii").Collection("post")
	/*
		insertResult, err := collection.InsertOne(context.TODO(), post2)
		if err != nil {
			log.Fatal(err)
		}
		fmt.Println("Inserted a single document: ", insertResult.InsertedID)

		var result models.Post
		filter := bson.D{primitive.E{Key: "id", Value: 1}}
		result.FindPostByID(primitive.NewObjectID())
		err = collection.FindOne(context.TODO(), filter).Decode(&result)
		fmt.Printf("Found a single document: %+v\n", result)
	*/
	result := models.FindPostByID("5deb9a1eb19bc5a841718027")
	fmt.Printf("Found a single document: %+v\n", result)

	// fileter :=
	router := gin.Default()
	router.GET("/ping", func(c *gin.Context) {
		c.JSON(200, gin.H{
			"message": "pong",
		})
	})
	router.Run()
}
