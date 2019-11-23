package main

import (
	"context"
	"fmt"
	"log"

	"github.com/gin-gonic/gin"
	"go.mongodb.org/mongo-driver/bson"
	"go.mongodb.org/mongo-driver/mongo"
	"go.mongodb.org/mongo-driver/mongo/options"
)

// Post is jiaolian
type Post struct {
	ID       int64
	Title    string
	Content  string
	Type     int
	Ctime    int
	Utime    int
	UserID   int
	ReferURL string
	Extra    string
}

func main() {
	clientOptions := options.Client().ApplyURI("mongodb+srv://cikii:1Fys0F1vzWjKkKGE@cluster0-oqjxi.azure.mongodb.net/test?retryWrites=true&w=majority")
	client, err := mongo.Connect(context.TODO(), clientOptions)
	if err != nil {
		log.Fatal(err)
	}
	err = client.Ping(context.TODO(), nil)
	if err != nil {
		log.Fatal(err)
	}
	fmt.Println("Connected to MongoDB")
	// post := Post{1, "这是一个title", "这是content，有长度", 1, 1574507828, 1574507828, 1, "http://www.baidu.com", ""}
	collection := client.Database("cikii").Collection("post")

	// insertResult, err := collection.InsertOne(context.TODO(), post)
	// if err != nil {
	// 	log.Fatal(err)
	// }
	// fmt.Println("Inserted a single document: ", insertResult.InsertedID)

	var result Post
	filter := bson.D{{"id", 1}}
	err = collection.FindOne(context.TODO(), filter).Decode(&result)
	fmt.Printf("Found a single document: %+v\n", result)

	router := gin.Default()
	router.GET("/ping", func(c *gin.Context) {
		c.JSON(200, gin.H{
			"message": "pong",
		})
	})
	router.Run()
}
