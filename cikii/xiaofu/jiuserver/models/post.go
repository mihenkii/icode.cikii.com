package models

import (
	"log"

	"go.mongodb.org/mongo-driver/bson"
	"go.mongodb.org/mongo-driver/bson/primitive"
	"go.mongodb.org/mongo-driver/mongo"
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

const (
	db             = "cikii"
	postCollection = "post"
)

// GetTitle method
func (p *Post) GetTitle() (title string) {
	return p.Title
}

// GetID method
func (p *Post) GetID() (ID primitive.ObjectID) {
	return p.ID
}

// CreatePost function
func CreatePost(post Post) *mongo.InsertOneResult {
	return Insert(db, postCollection, post)
}

// UpdatePost method
func UpdatePost(post Post) {
	filter := bson.D{primitive.E{Key: "_id", Value: post.ID}}
	update := bson.D{
		{Key: "$set", Value: bson.D{{Key: "title", Value: "tttt"}}},
	}
	Update(db, postCollection, filter, update)
}

// MultiInsertPost method
func (p *Post) MultiInsertPost(posts []Post) *mongo.InsertManyResult {
	return InsertMany(db, postCollection, posts)
}

// FindPostByID method
func FindPostByID(id string) (result interface{}) {
	oid, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Fatal(err)
	}
	filter := bson.D{primitive.E{Key: "_id", Value: oid}}
	return FindOne(db, postCollection, filter)
}
