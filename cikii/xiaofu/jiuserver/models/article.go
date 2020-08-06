package models

import (
	"go.mongodb.org/mongo-driver/bson/primitive"
)

// Article is artile,  如果_id没加omitempty tag，上层只透传其他字段，会给ID自动设置成0000000, 这样无法找到记录.
type Article struct {
	ID       primitive.ObjectID `bson:"_id,omitempty" json:"id,omitempty"`
	Title    string             `bson:"title,omitempty" json:"title,omitempty" form:"title,omitempty"`
	Content  string             `bson:"content,omitempty" json:"content,omitempty" form:"content,omitempty"`
	Type     int                `bson:"type,omitempty" json:"type,omitempty" form:"type,omitempty"`
	Tag      []int              `bson:"tag,omitempty" json:"tag,omitempty" form:"tag,omitempty"`
	Ctime    int64              `bson:"ctime,omitempty" json:"ctime,omitempty" form:"var1,omitempty"`
	Utime    int64              `bson:"utime,omitempty" json:"utime,omitempty" form:"var1,omitempty"`
	UserID   int                `bson:"user_id,omitempty" json:"user_id,omitempty" form:"var1,omitempty"`
	ReferURL string             `bson:"refer_url,omitempty" json:"refer_url,omitempty" form:"var1,omitempty"`
	Extra    string             `bson:"extra,omitempty" json:"extra,omitempty" form:"var1,omitempty"`
}
