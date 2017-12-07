#!/usr/bin/python
# FileName Wmpic_Spider.py
#coding=utf-8

import os
import json
import sys
import re
import time
import requests
import random
from lxml import html

reload(sys)
sys.setdefaultencoding('utf-8')


class WmpicSpider:
    def __init__(self, url, uri):
        self.url = url
        self.uri = uri

    def getHtml(self, url, timeout=20):
        try:
            headers = {
                'Accept-Language': 'zh-cn',
                'Content-Type': 'application/x-www-form-urlencoded',
                'User-Agent': 'Mozilla/4.0 (compatible MSIE 6.00 Windows NT 5.1 SV1)',
            }
            page = requests.get(url, headers=headers, timeout=timeout)

            charset = "utf-8"
            m = re.compile('<meta .*(http-equiv="?Content-Type"?.*)?charset="?([a-zA-Z0-9_-]+)"?', re.I).search(page.text)
            if m and m.lastindex == 2:
                charset = m.group(2).lower()
            page.encoding = charset
            return html.fromstring(page.text)
        except Exception,ex:
           return None

    def getThemeList(self, stochastic = False):
       url = self.url + "/" + self.uri
       themeList = self.getHtml(url)
       themeList = themeList.xpath('//*[@id="device"]/li/a//@href')
       if(stochastic == True):
           randomNum = random.randint(1, len(themeList)-1)
           return themeList[randomNum]
       return themeList  
    
    def getImgUrl(self, stochastic = False):
       uri = self.getThemeList(True)
       url = self.url + uri
       imgList = self.getHtml(url)
       imgList =  imgList.xpath('//div[@class="content-c"]//img//@src')
       if(stochastic == True):
           randomNum = random.randint(1, len(imgList)-1)
           return imgList[randomNum]
       return imgList  

wmpic = WmpicSpider('http://www.wmpic.me', "hot")
print wmpic.getImgUrl(True)
        
