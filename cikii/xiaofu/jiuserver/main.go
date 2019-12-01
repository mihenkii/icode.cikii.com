package main

import (
	"context"
	"fmt"
	"log"
	"time"

	"github.com/gin-gonic/gin"
	"go.mongodb.org/mongo-driver/bson"
	"go.mongodb.org/mongo-driver/bson/primitive"
	"go.mongodb.org/mongo-driver/mongo"
	"go.mongodb.org/mongo-driver/mongo/options"
	"icode.cikii.com/cikii/xiaofu/jiuserver/models"
)

// Post is artile
type Post struct {
	ID       primitive.ObjectID `bson:"_id" json:"id"`
	Title    string             `bson:"title" json:"title"`
	Content  string             `bson:"content" json:"content"`
	Type     int                `bson:"type" json:"type"`
	Ctime    int64              `bson:"ctime" json:"ctime"`
	Utime    int64              `bson:"utime" json:"utime"`
	UserID   int                `bson:"user_id" json:"user_id"`
	ReferURL string             `bson:"refer_url" json:"refer_url"`
	Extra    string             `bson:"extra" json:"extra"`
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
	post := models.Post{}
	post.ID = primitive.NewObjectID()
	post2 := Post{primitive.NewObjectID(), "这是一个title", "这是content，有长度", 1, time.Now().Unix(), time.Now().Unix(), 1, "http://www.baidu.com", ""}
	collection := client.Database("cikii").Collection("post")

	insertResult, err := collection.InsertOne(context.TODO(), post2)
	if err != nil {
		log.Fatal(err)
	}
	fmt.Println("Inserted a single document: ", insertResult.InsertedID)

	var result Post
	filter := bson.D{primitive.E{Key: "id", Value: 1}}
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
