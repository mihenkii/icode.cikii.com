package main

import (
	"context"
	"fmt"
	"log"

	"github.com/gin-gonic/gin"
	// "go.mongodb.org/mongo-driver/bson"
	"go.mongodb.org/mongo-driver/mongo"
	"go.mongodb.org/mongo-driver/mongo/options"
)

type Trainer struct {
	Name	string
	Age	int
	City	string
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
	router := gin.Default()
	router.GET("/ping", func(c *gin.Context) {
		c.JSON(200, gin.H{
			"message": "pong",
		})
	})
	router.Run()
}
