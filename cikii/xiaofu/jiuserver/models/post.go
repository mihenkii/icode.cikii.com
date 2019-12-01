package models

import (
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

// InsertPost method
func (p *Post) InsertPost(post Post) *mongo.InsertOneResult {
	return Insert(db, postCollection, post)
}

// MultiInsertPost method
func (p *Post) MultiInsertPost(posts []Post) *mongo.InsertManyResult {
	return InsertMany(db, postCollection, posts)
}

// FindPostByID method
func (p *Post) FindPostByID(ID primitive.ObjectID) (result interface{}) {
	var post Post
	post.ID = ID
	return FindOne(db, postCollection, post)
}
