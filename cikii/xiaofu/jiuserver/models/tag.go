package models

import (
	"log"

	"go.mongodb.org/mongo-driver/bson"
	"go.mongodb.org/mongo-driver/bson/primitive"
	"go.mongodb.org/mongo-driver/mongo"
)

// Tag is artile,  如果_id没加omitempty tag，上层只透传其他字段，会给ID自动设置成0000000, 这样无法找到记录.
type Tag struct {
	ID    primitive.ObjectID `bson:"_id,omitempty" json:"id,omitempty" form:"id,omitempty" uri:"id"`
	Name  string             `bson:"name,omitempty" json:"name,omitempty" form:"name,omitempty" uri:"name"`
	State int                `bson:"state,omitempty" json:"state,omitempty" form:"state,omitempty" uri:"state"`
}

// NewTag always next to Post struct define
func NewTag() *Tag {
	return &Tag{}
}

const (
	tagCollection = "tag"
)

// GetName method
func (t *Tag) GetName() (name string) {
	return t.Name
}

// GetID method
func (t *Tag) GetID() (ID primitive.ObjectID) {
	return t.ID
}

// CreateTag function
func CreateTag(tag Tag) (*mongo.InsertOneResult, error) {
	return Insert(db, tagCollection, tag)
}

// DeleteTag function
func DeleteTag(tag Tag) (*mongo.DeleteResult, error) {
	filter, err := ConvertToDoc(tag)
	if err != nil {
		log.Printf("%v", tag)
	}
	return Delete(db, tagCollection, filter)
}

// DeleteTagByID method
func DeleteTagByID(id string) (*mongo.DeleteResult, error) {
	oid, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Fatal(err)
	}
	filter := bson.D{primitive.E{Key: "_id", Value: oid}}
	return Delete(db, tagCollection, filter)
}

// UpdateTag method
func UpdateTag(tag Tag) (*mongo.UpdateResult, error) {
	filter := bson.D{{Key: "_id", Value: tag.ID}}
	update2, err := ConvertToDoc(tag)
	if err != nil {
		log.Fatal(err)
	}
	update3 := bson.D{{Key: "$set", Value: update2}}
	log.Printf("%v", update2)
	return Update(db, tagCollection, filter, update3)
}

// MultiInsertTag method
func (t *Tag) MultiInsertTag(tags []Tag) *mongo.InsertManyResult {
	return InsertMany(db, tagCollection, tags)
}

// GetTagByID method
func GetTagByID(id string) (result interface{}) {
	oid, err := primitive.ObjectIDFromHex(id)
	if err != nil {
		log.Fatal(err)
	}
	filter := bson.D{primitive.E{Key: "_id", Value: oid}}
	return FindOne(db, tagCollection, filter)
}

// FindTagByName method
func FindTagByName(name string) (result interface{}) {
	if name == "" {
		log.Printf("FindTagByName where name is empty")
	}
	filter := bson.D{primitive.E{Key: "name", Value: name}}
	return FindOne(db, tagCollection, filter)
}

// FindTagPagination method
func FindTagPagination(offset, limit int64) ([]bson.M, error) {
	ret := FindManyPagination(db, tagCollection, bson.D{}, offset, limit)
	return ret, nil
}

// FindTagByField method
func FindTagByField(conds *bson.M) ([]bson.M, error) {
	ret := FindMany(db, tagCollection, conds)
	return ret, nil
}
