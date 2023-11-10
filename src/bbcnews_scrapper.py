#!/usr/bin/python3

import requests
from bs4 import BeautifulSoup
import urllib.parse
import psycopg2

base_url = 'https://www.bbc.com/news/reality_check'  # Replace with the base URL of the website
output_file = 'bbcnews_scraped_data.json'  # Name of the output JSON file

# Send an HTTP GET request to the website
response = requests.get(base_url)

# Check if the request was successful
if response.status_code == 200:
    # Parse the HTML content of the page
    soup = BeautifulSoup(response.text, 'html.parser')

    # Find the <ul> element with class "bloglist"
    bloglist_ul = soup.find_all('ul', class_='ssrcss-5ahces-Grid e12imr580')

    if bloglist_ul:
        # Initialize blog_data outside the loop to collect data for all articles
        blog_data = []

        # Find all <li> elements within the <ul>
        for item in bloglist_ul:
            li_elements = item.find_all('li', recursive=False)

            for li in li_elements:
                # Find and print the link within each <li> element
                link = li.find('a')

                if link:
                    href = link.get('href')
                    full_link = urllib.parse.urljoin(base_url, href)

                    # Extract image URL
                    img_element = li.find('img')
                    img_url = img_element['src'] if img_element else ""

                    # Send an HTTP GET request to the article page to scrape the content
                    article_response = requests.get(full_link)

                    if article_response.status_code == 200:
                        article_soup = BeautifulSoup(article_response.text, 'html.parser')

                        # Extract content from the individual news article
                        content = article_soup.find('article', class_='ssrcss-pv1rh6-ArticleWrapper e1nh2i2l6').text

                        blog_data.append({
                            'title': li.find('a').text,
                            'link': full_link,
                            'img_url': img_url,
                            'content': content,
                            'source': 'BBC News',
                        })

        scraped_data = {'posts': blog_data}
        # Serialize and save the data as a JSON file
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

        print(f"Scraped data from BBC NEWS has been saved to the database.")
    else:
        print("No element with class 'ssrcss-5ahces-Grid e12imr580' found on the page.")
else:
    print(f"Failed to retrieve the webpage. Status code: {response.status_code}")

