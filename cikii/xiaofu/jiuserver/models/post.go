package models

import (
	"log"

	"go.mongodb.org/mongo-driver/bson"
	"go.mongodb.org/mongo-driver/bson/primitive"
	"go.mongodb.org/mongo-driver/mongo"
)

// Post is artile,  如果_id没加omitempty tag，上层只透传其他字段，会给ID自动设置成0000000, 这样无法找到记录.
type Post struct {
	ID       primitive.ObjectID `bson:"_id,omitempty" json:"id,omitempty"`
	Title    string             `bson:"title,omitempty" json:"title,omitempty"`
	Content  string             `bson:"content,omitempty" json:"content,omitempty"`
	Type     int                `bson:"type,omitempty" json:"type,omitempty"`
	Ctime    int64              `bson:"ctime,omitempty" json:"ctime,omitempty"`
	Utime    int64              `bson:"utime,omitempty" json:"utime,omitempty"`
	UserID   int                `bson:"user_id,omitempty" json:"user_id,omitempty"`
	ReferURL string             `bson:"refer_url,omitempty" json:"refer_url,omitempty"`
	Extra    string             `bson:"extra,omitempty" json:"extra,omitempty"`
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

// DeletePost function
func DeletePost(post Post) *mongo.DeleteResult {
	filter, err := ConvertToDoc(post)
	if err != nil {
		log.Printf("%v", post)
	}
	return Delete(db, postCollection, filter)
}

// UpdatePost method
func UpdatePost(post Post) {
	filter := bson.D{{Key: "_id", Value: post.ID}}
	update2, err := ConvertToDoc(post)
	if err != nil {
		log.Fatal(err)
	}
	// update := bson.D{
	//	{Key: "$set", Value: bson.D{{Key: "title", Value: post.Title}}},
	// }
	// update := bson.M{{Key: "title", Value: post.Title}}}
	// update1 := bson.D{{Key: "$set", Value: bson.M{"title": post.Title, "Content": post.Content}}}
	update3 := bson.D{{Key: "$set", Value: update2}}
	log.Printf("%v", update2)
	Update(db, postCollection, filter, update3)
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

// FindAllPost method
// func FindAllPost() ([]interface{}, error) {
func FindAllPost() ([]bson.M, error) {
	ret := FindMany(db, postCollection, bson.D{}, 2, 3)
	return ret, nil
}
