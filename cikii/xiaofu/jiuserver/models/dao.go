package models

import (
	"context"
	"fmt"
	"log"

	"go.mongodb.org/mongo-driver/bson"
	"go.mongodb.org/mongo-driver/mongo"
	"go.mongodb.org/mongo-driver/mongo/options"
)

const (
	mongodbURI = "mongodb+srv://cikii:1Fys0F1vzWjKkKGE@cluster0-oqjxi.azure.mongodb.net/test?retryWrites=true&w=majority"
)

var mongoClient *mongo.Client

// var collection *mongo.Collection

func init() {
	clientOptions := options.Client().ApplyURI(mongodbURI)
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
func Insert(db, collection string, doc interface{}) *mongo.InsertOneResult {
	conn := connect(db, collection)
	insertResult, err := conn.InsertOne(context.TODO(), doc)
	if err != nil {
		log.Fatal(err)
	}
	fmt.Println("Inserted a single document: ", insertResult.InsertedID)
	return insertResult
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
func FindOne(db, collection string, filter interface{}) (result interface{}) {
	conn := connect(db, collection)
	err := conn.FindOne(context.TODO(), filter).Decode(&result)
	if err != nil {
		log.Fatal(err)
	}
	fmt.Printf("Found a single document: %+v\n", result)
	return result
}

// FindMany documents
func FindMany(db, collection string, filter interface{}, offset, limit int64) (results []interface{}) {
	conn := connect(db, collection)
	findOptions := options.Find()
	findOptions.SetLimit(limit)
	findOptions.SetSkip(offset)

	cur, err := conn.Find(context.TODO(), filter, findOptions)
	if err != nil {
		log.Fatal(err)
	}

	for cur.Next(context.TODO()) {
		var item interface{}
		err := cur.Decode(&item)
		if err != nil {
			log.Fatal(err)
		}
		results = append(results, &item)
	}
	if err := cur.Err(); err != nil {
		log.Fatal(err)
	}
	cur.Close(context.TODO())
	fmt.Printf("Found multiple documents (array of pointers): %+v\n", results)
	return results
}

// Update document
func Update(db, collection string, filter, update interface{}) *mongo.UpdateResult {
	conn := connect(db, collection)
	updateResult, err := conn.UpdateOne(context.TODO(), filter, update)
	if err != nil {
		log.Fatal(err)
	}
	fmt.Printf("Matched %v documents and updated %v documents.\n", updateResult.MatchedCount, updateResult.ModifiedCount)
	return updateResult
}

// Delete document
func Delete(db, collection string, filter interface{}) *mongo.DeleteResult {
	conn := connect(db, collection)
	deleteResulct, err := conn.DeleteOne(context.TODO(), filter)
	if err != nil {
		log.Fatal(err)
	}
	fmt.Printf("Deleted %v document in the collection\n", deleteResulct.DeletedCount)
	return deleteResulct
}

// ConvertToDoc to document
func ConvertToDoc(v interface{}) (doc *bson.M, err error) {
	data, err := bson.Marshal(v)
	if err != nil {
		return
	}
	err = bson.Unmarshal(data, &doc)
	return
}
