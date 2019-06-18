import _ from "lodash";
import axios from "axios";
import SETTINGS from "../settings";

export default {
  getCategories(cb) {
    axios
      .get(
        SETTINGS.API_BASE_PATH +
        "categories?sort=name&hide_empty=true&per_page=50"
      )
      .then(response => {
        cb(response.data.filter(c => c.name !== "Uncategorized"));
      })
      .catch(e => {
        cb(e);
      });
  },

  getPages(cb) {
    axios
      .get(SETTINGS.API_BASE_PATH + "pages?per_page=10")
      .then(response => {
        cb(response.data);
      })
      .catch(e => {
        cb(e);
      });
  },

  getPage(id, cb) {
    if (_.isNull(id) || !_.isNumber(id)) return false;
    axios
      .get(SETTINGS.API_BASE_PATH + "pages/" + id)
      .then(response => {
        cb(response.data);
      })
      .catch(e => {
        cb(e);
      });
  },

  getPosts(limit, cb) {
    if (_.isEmpty(limit)) {
      let limit = 5;
    }

    axios
      .get(SETTINGS.API_BASE_PATH + "posts?per_page=" + limit)
      .then(response => {
        cb(response.data);
      })
      .catch(e => {
        cb(e);
      });
  },

  getProjects(limit, cb) {
    if (_.isEmpty(limit)) {
      let limit = 20;
    }

    axios
      .get(SETTINGS.API_BASE_PATH + "portfolios?per_page=" + limit)
      .then(response => {
        cb(response.data);
      })
      .catch(e => {
        cb(e);
      });
  },
  
  // New APIs
  getColors(cb) {
    axios
      .get(SETTINGS.API_BASE_PATH + "portfolio_colors")
      .then(response => {
        cb(response.data);
      })
      .catch(e => {
        cb(e);
      });
  },
  getTags(cb) {
    axios
      .get(SETTINGS.API_BASE_PATH + "portfolio_tags")
      .then(response => {
        cb(response.data);
      })
      .catch(e => {
        cb(e);
      });
  },
  getProductType(cb) {
    axios
      .get(SETTINGS.API_BASE_PATH + "product_type")
      .then(response => {
        cb(response.data);
      })
      .catch(e => {
        cb(e);
      });
  },
  getPortfolioCats(cb) {
    axios
      .get(SETTINGS.API_BASE_PATH + "portfolio_categories")
      .then(response => {
        cb(response.data);
      })
      .catch(e => {
        cb(e);
      });
  }
};