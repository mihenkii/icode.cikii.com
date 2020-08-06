package config

import (
	"fmt"
	"io/ioutil"
	"time"

	"gopkg.in/yaml.v2"
)

// ServerConfig for server
type ServerConfig struct {
	Host         string        `yaml:"host" envconfig:"SERVER_HOST"`
	Port         uint16        `yaml:"port" envconfig:"SERVER_PORT"`
	ReadTimeout  time.Duration `yaml:"readtimeout"`
	WriteTimeout time.Duration `yaml:"writetimeout"`
}

// MongodbConfig for mongodb
type MongodbConfig struct {
	MongodbURI string `yaml:"mongodbURI"`
}

// Config file
type Config struct {
	RunMode string
	Server  ServerConfig  `yaml:"server"`
	MongoDB MongodbConfig `yaml:"mongodb"`
}

var defaultConfig = Config{
	RunMode: "debug",
	Server:  ServerConfig{Host: "localhost", Port: 8012},
	// MongoDB: MongodbConfig{MongodbURI: "mongodb+srv://cikii:9NRYuOtb3N1AVya2@cluster0-oqjxi.azure.mongodb.net/test?retryWrites=true&w=majority"},
	MongoDB: MongodbConfig{MongodbURI: "mongodb://127.0.0.1:27017"},
}

// Process validate config format
func (c *Config) Process() error {
	var checkConfErr error
	if c.RunMode != "prod" {
		c.RunMode = "debug"
		return checkConfErr
	}
	return nil
}

// Initalize method
func (c *Config) Initalize(configYAML []byte) error {
	return yaml.Unmarshal(configYAML, &c)
}

// DefaultConfig method
func DefaultConfig() (*Config, error) {
	c := defaultConfig
	err := c.Process()
	if err != nil {
		return nil, fmt.Errorf("invalid default config: %s", err)
	}
	return &c, err
}

// InitConfigFromFile method
func InitConfigFromFile(path string) (*Config, error) {
	c, err := DefaultConfig()
	if err != nil {
		return nil, err
	}
	b, err := ioutil.ReadFile(path)
	if err != nil {
		return nil, err
	}
	err = c.Initalize(b)
	if err != nil {
		return nil, err
	}
	return c, nil
}
