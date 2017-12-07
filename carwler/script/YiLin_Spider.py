#!/usr/bin/python2.7
# FileNmae:YiLin_Spider.py
#coding=utf-8

import os
import json
import sys
import re
import time
import requests
from lxml import html 

reload(sys)
sys.setdefaultencoding('utf-8')


class YiLinSpider:
    def __init__(self, url):
        self.url = url

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

    def getJournalListTree(self):
        journal = self.getHtml(self.url)
        journalInfo = journal.xpath('//*[@id="tagContent0"]/table/tr/td/a/@href')
       # journalInfo = [self.url + "/" +item for item in journalInfo]
        return sorted(journalInfo, reverse = True)

    def getClassListTree(self, uri):
        url = self.url + "/" + uri
        classItem = self.getHtml(url)
        classList = classItem.xpath('//div[@class="maglistbox"]/dl/dt/span')
        pattern = re.compile(u'[\u4e00-\u9fa5]+')
        classList = ["".join(pattern.findall(unicode(item.text))) for item in classList]
        return classList

    def getArticleListTree(self, uri, id):
        url = self.url + "/" + uri
        articleItem = self.getHtml(url)
        rule = '//div[@class="maglistbox"]/dl[' + str(id) + ']/dd/span/a/@href' 
        articleList = articleItem.xpath(rule);
        #articleList = [item.text for item in articleList]
        return articleList
    
    def getContent(self, uri):
        url = self.url + "/" + uri
        content = self.getHtml(url)
        contentInfo = content.xpath('//div[@class="blkContainerSblkCon"]//p')
        title = content.xpath('//div[@class="blkContainerSblk collectionContainer"]/h1')
        author = content.xpath('//span[@id="pub_date"]')
        pattern = re.compile(u'[\u4e00-\u9fa5]+')
        title = "".join(pattern.findall(unicode(title[0].text)))
        author = "".join(pattern.findall(unicode(author[0].text)))
        contentList = [item.text for item in contentInfo]
        contentInfo.insert(0, title)
        contentInfo.insert(1, author)
        try: 
            contentInfo.insert(2, "".join(contentList))
        except Exception,ex:
            contentInfo.insert(2, contentList[0])
        return contentInfo

    def getLog(self, tag):
        filename = '/var/www/wechat/carwler/log/execute.log'
        fp = open(filename, "r")
        lines = fp.readlines()
        for itemStr in lines:
            item = json.loads(itemStr)
            if(item != "\n" and item['type'] == tag):
                logInfo = item
        fp.close()
        return logInfo        

    def setLog(self, logInfo):
        filename = '/var/www/wechat/carwler/log/execute.log'
        logStr = json.dumps(logInfo)
        fp = open(filename, "w")
        fp.write(logStr)
        fp.close()

    def getWeb(self):
        listItem = self.getJournalListTree()
        classify = []
        count = 0
        uri = ''
        savePath = "/var/www/wechat/carwler/resource/text/"
        for Item in listItem:
            uri = Item.replace("/index.html", "")
            lastCurrentLog = self.getLog('yilin');
            if(cmp(uri, lastCurrentLog['uri']) > 0):
                classListTree = self.getClassListTree(uri)
                index = 0;
                for classItem in classListTree:
                    filePath = savePath + classItem.strip()
                    if(not os.path.exists(filePath)):
                        print "Create Dir Name:" , classItem.strip() , " from " , uri
                        os.makedirs(filePath)
                    index += 1
                    articleList = self.getArticleListTree(uri, index)
                    for articleItem in articleList:
                        ++count
                        url = uri + "/" +articleItem
                        try:
                            content = self.getContent(url)
                            fileName = filePath + "/" + uri + "_" + content[0] + ".txt"
                            fp = open(fileName, "w+")
                            fp.write(content[0])
                            fp.write(content[1])
                            fp.write(content[2])
                            fp.close()
                            print "get -" + uri + " - " + classItem.strip() + " - " + fileName + " - done"
                        except Exception,ex:
                            print 'jump over'
                            continue 
        logInfo = {'type' : 'yilin', 'date' : time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(time.time())), 'uri' : '2017_08', 'count' : count} 
        yilin.setLog(logInfo)


yilin = YiLinSpider('http://www.92yilin.com')
yilin.getWeb();
