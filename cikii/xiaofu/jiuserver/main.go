package main

import (
	"flag"
	"fmt"
	"log"
	"net/http"

	"go.mongodb.org/mongo-driver/bson/primitive"
	"icode.cikii.com/cikii/xiaofu/jiuserver/config"
	"icode.cikii.com/cikii/xiaofu/jiuserver/models"
	"icode.cikii.com/cikii/xiaofu/jiuserver/routers"
)

var (
	configFile string
)

func main() {
	flag.StringVar(&configFile, "c", "", "Configure File")
	flag.Parse()
	c, err := config.DefaultConfig()
	if err != nil {
		log.Fatalf("Error loading  default config")
	}
	if configFile != "" {
		c, err = config.InitConfigFromFile(configFile)
		if err != nil {
			log.Fatalf("Error loading config: %s", configFile)
		}
	}
	log.Println(c)

	models.InitDB(c)

	tag := models.Tag{}
	tag.ID = primitive.NewObjectID()
	tag.Name = "classic"

	models.CreateTag(tag)

	// post := models.Post{primitive.NewObjectID(), "这是一个title3", "这是content，有长度", 1, time.Now().Unix(), time.Now().Unix(), 1, "http://www.baidu.com", ""}
	/*
		var post models.Post
		post.ID = primitive.NewObjectID()
		post.Title = "这是一个title4"
		post.Content = "这是content，有长度"
		post.Type = 1
		post.Ctime = time.Now().Unix()
		post.Utime = time.Now().Unix()
		post.UserID = 1
		post.ReferURL = "http://www.baidu.com"
		post.Extra = ""
		models.CreatePost(post)

		result := models.FindPostByID("5deb9a1eb19bc5a841718027")
		fmt.Printf("Found a single document: %+v\n", result)

		var postForUpdate models.Post
		oid, err := primitive.ObjectIDFromHex("5deb9a1eb19bc5a841718027")
		if err != nil {
			log.Fatal(err)
		}
		postForUpdate.ID = oid
		postForUpdate.Title = "new title M2"
		models.UpdatePost(postForUpdate)

		result = models.FindPostByID("5deb9a1eb19bc5a841718027")
		fmt.Printf("Found a single document: %+v\n", result)

		var postToDelete models.Post
		postToDelete.Ctime = 1575188641
		ret := models.DeletePost(postToDelete)
		if ret.DeletedCount == 1 {
			log.Printf("delete ret: %v", ret)
		}
	*/

	allPost, err := models.FindAllPost()
	if err != nil {
		log.Fatal(err)
	}
	for index, item := range allPost {
		log.Printf("index %d\t", index)
		log.Printf("value %v \n", item)
		/*
			log.Printf("allPost %d ret: %+v\n", index, item)
			log.Printf("print %+v\n", reflect.ValueOf(item))
			value := reflect.ValueOf(item)
			// pvalue := value.Interface().Elem()
			pvalue := value.Elem()
			log.Printf(" pvalue is :%+v", pvalue)
		*/

	}

	// fileter :=

	router := routers.InitRouter()

	s := &http.Server{
		Addr:         fmt.Sprintf(":%d", c.Server.Port),
		Handler:      router,
		ReadTimeout:  c.Server.ReadTimeout,
		WriteTimeout: c.Server.ReadTimeout,
	}
	s.ListenAndServe()
	// router.Run()
}
