#!/usr/bin/python3
import requests
from bs4 import BeautifulSoup
import urllib.parse
import psycopg2

base_url = "https://opennews.org/blog/"

# Send an HTTP GET request to the website
response = requests.get(base_url)

# Check if the request was successful
if response.status_code == 200:
    # Parse the HTML content of the page
    soup = BeautifulSoup(response.text, 'html.parser')

    # Find the <ul> element with class "bloglist"
    bloglist_ul = soup.find('ul', class_='bloglist')

    if bloglist_ul:
        # Find all <li> elements within the <ul>
        blog_posts = bloglist_ul.find_all('li')

        # Create a list to store the data for each blog
        blog_data = []

        # Iterate through the list items and store the data
        for post in blog_posts:
            relative_link = post.find('a')['href']
            full_link = urllib.parse.urljoin(base_url, relative_link)

            # Extract image URL
            img_element = post.find('img')
            img_url = img_element['src'] if img_element else ""

            # Additional details from individual news articles
            article_response = requests.get(full_link)
            if article_response.status_code == 200:
                article_soup = BeautifulSoup(article_response.text, 'html.parser')
                content = article_soup.find('div', class_='main_col').text
            else:
                content = "Content not found"

            blog_data.append({
                'title': post.find('a').text,
                'link': full_link,
                'img_url': img_url,
                'content': content,
                'source': 'Web News',
            })

        # Create a connection to the PostgreSQL database
        conn = psycopg2.connect(
            dbname="new_management_system",
            user="talhazee",
            password="talhazee",
            host="db"
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

        print(f"Scraped data from WEB NEWS has been saved to the PostgreSQL database.")
    else:
        print("No <ul> element with class 'bloglist' found on the page.")

else:
    print(f"Failed to retrieve the webpage. Status code: {response.status_code}")
