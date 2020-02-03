package models

import (
	"context"
	"fmt"
	"log"
	"time"

	"go.mongodb.org/mongo-driver/bson"
	"go.mongodb.org/mongo-driver/mongo"
	"go.mongodb.org/mongo-driver/mongo/options"

	"icode.cikii.com/cikii/xiaofu/jiuserver/config"
)

const (
	// mongodbURI = "mongodb+srv://cikii:1Fys0F1vzWjKkKGE@cluster0-oqjxi.azure.mongodb.net/test?retryWrites=true&w=majority"
	mongodbURI = "mongodb://10.232.220.42:8017"
)

var db = "cikii"
var mongoClient *mongo.Client

// var collection *mongo.Collection

// InitDB init mongodb connection
func InitDB(c *config.Config) {
	clientOptions := options.Client().ApplyURI(c.MongoDB.MongodbURI)
	client, err := mongo.Connect(context.TODO(), clientOptions)
	if err != nil {
		log.Fatal(err)
	}
	err = client.Ping(context.TODO(), nil)
	if err != nil {
		log.Fatal(err)
	}
	mongoClient = client
	fmt.Println("Connected to MongoDB")
}

func connect(db, collection string) *mongo.Collection {
	return mongoClient.Database(db).Collection(collection)
}

// Insert one document
func Insert(db, collection string, doc interface{}) (*mongo.InsertOneResult, error) {
	conn := connect(db, collection)
	insertResult, err := conn.InsertOne(context.TODO(), doc)
	// fmt.Println("Inserted a single document: ", insertResult.InsertedID)
	return insertResult, err
}

// InsertMany multi insert documents
func InsertMany(db, collection string, docs ...interface{}) *mongo.InsertManyResult {
	conn := connect(db, collection)
	insertManyResult, err := conn.InsertMany(context.TODO(), docs)
	if err != nil {
		log.Fatal(err)
	}
	fmt.Println("Inserted multiple documents: ", insertManyResult.InsertedIDs)
	return insertManyResult
}

// FindOne document
func FindOne(db, collection string, filter interface{}) (result bson.M) {
	conn := connect(db, collection)
	err := conn.FindOne(context.TODO(), filter).Decode(&result)
	if err != nil {
		log.Printf("FindOne get err: %s", err)
	}
	fmt.Printf("Found a single document: %+v\n", result)
	return result
}

// FindManyPagination documents
func FindManyPagination(db, collection string, filter interface{}, offset, limit int64) (results []bson.M) {
	conn := connect(db, collection)
	findOptions := options.Find()
	findOptions.SetSkip(offset)
	findOptions.SetLimit(limit)

	cur, err := conn.Find(context.TODO(), filter, findOptions)
	if err != nil {
		log.Fatal(err)
	}
	if err = cur.All(context.TODO(), &results); err != nil {
		log.Fatal(err)
	}
	/*
		for cur.Next(context.TODO()) {
			var item bson.M
			err := cur.Decode(&item)
			if err != nil {
				log.Fatal(err)
			}
			results = append(results, item)
		}
		if err := cur.Err(); err != nil {
			log.Fatal(err)
		}
		cur.Close(context.TODO())
		fmt.Printf("Found multiple documents (array of pointers): %+v\n", results)
	*/
	return
}

// FindMany methond
func FindMany(db, collection string, filter interface{}) (results []bson.M) {
	conn := connect(db, collection)
	findOptions := options.Find()

	cur, err := conn.Find(context.TODO(), filter, findOptions)
	if err != nil {
		log.Fatal(err)
	}
	if err = cur.All(context.TODO(), &results); err != nil {
		log.Fatal(err)
	}
	return
}

// Update document
func Update(db, collection string, filter, update interface{}) (*mongo.UpdateResult, error) {
	conn := connect(db, collection)
	updateResult, err := conn.UpdateOne(context.TODO(), filter, update)
	if err != nil {
		log.Fatal(err)
	}
	fmt.Printf("Matched %v documents and updated %v documents.\n", updateResult.MatchedCount, updateResult.ModifiedCount)
	return updateResult, err
}

// Delete document
func Delete(db, collection string, filter interface{}) (*mongo.DeleteResult, error) {
	conn := connect(db, collection)
	deleteResulct, err := conn.DeleteOne(context.TODO(), filter)
	fmt.Printf("Deleted %v document in the collection\n", deleteResulct.DeletedCount)
	return deleteResulct, err
}

// Count document
func Count(db, collection string, filter interface{}) (int64, error) {
	opts := options.Count().SetMaxTime(2 * time.Second)
	conn := connect(db, collection)
	count, err := conn.CountDocuments(context.TODO(), filter, opts)
	if err != nil {
		fmt.Printf("Count by %v filter is: %d\n", filter, count)
	}
	return count, err
}

// ConvertToDoc convert struct to document
func ConvertToDoc(v interface{}) (doc *bson.M, err error) {
	data, err := bson.Marshal(v)
	if err != nil {
		return
	}
	err = bson.Unmarshal(data, &doc)
	return
}
