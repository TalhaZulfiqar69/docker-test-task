#!/usr/bin/python3
import requests
from bs4 import BeautifulSoup
import urllib.parse
import psycopg2

base_url = "https://www.theguardian.com/uk"
output_file = 'theguardian_scraped_data.json'  
response = requests.get(base_url)

if response.status_code == 200:
    soup = BeautifulSoup(response.text, 'html.parser')
    links_div= soup.find_all('div', class_='dcr-12ilguo')
    blog_data = []

    for post in links_div:

        relative_link = post.find('a')['href']
        base_url = base_url.rstrip('/uk')
        full_link = urllib.parse.urljoin(base_url, relative_link)

        article_response = requests.get(full_link)
        img_element = post.find('img')

        img_url = img_element['src'] if img_element else ""

        if article_response.status_code == 200:
            article_soup = BeautifulSoup(article_response.text, 'html.parser')
            content_div = article_soup.find('div', class_='dcr-1fd7bpz')
            if content_div:
                title_element = content_div.find('h1', class_='dcr-y70mar')
                if title_element:
                    content = content_div.text
                    title = title_element.text
                    blog_data.append({
                        'title': title,
                        'link': full_link,
                        'img_url': img_url,
                        'content': content,
                        'source': 'The Guardian News'
                    })
                else:
                    print("No Content found")
            else:
                print("No <content_div> element found on the page.")
        else:
            print(f"Failed to retrieve the article_response. Status code: {article_response.status_code}")
    # Create a connection to the PostgreSQL database
    conn = psycopg2.connect(
        dbname="new_management_system",
        user="talhazee",
        password="talhazee",
        host="127.0.0.1",
        port=5433,
    )

    # Create a cursor object to interact with the database
    cursor = conn.cursor()


    # Insert each blog post into the database
    for post in blog_data:
        cursor.execute(
            "INSERT INTO articles (title, link, img_url, content, source) VALUES (%s, %s, %s, %s, %s)",
            (post['title'], post['link'], post['img_url'], post['content'], post['source'])
        )

    # Commit the changes to the database
    conn.commit()

    # Close the cursor and connection
    cursor.close()
    conn.close()
    print(f"Scraped data from THE GUARIDAN NEWS has been saved to the database.")
else:
    print(f"Failed to retrieve the webpage. Status code: {response.status_code}")